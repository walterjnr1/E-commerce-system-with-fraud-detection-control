<?php
include('config.php');
include('inc/email_landing_page.php');

// --- Product Fetch ---
$products = [];
$sql = "SELECT id, name, description, image, price, stock_qty FROM products";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $products[] = $row;
  }
}

// --- Helper Functions ---
function fetchFraudWeight($conn, $signal_type, $scope = 'checkout') {
    $stmt = $conn->prepare("SELECT weight FROM fraud_rules WHERE signal_type=? AND scope=? LIMIT 1");
    $stmt->bind_param('ss', $signal_type, $scope);
    $stmt->execute();
    $weight = 0;
    $stmt->bind_result($weight);
    $w = 0;
    if ($stmt->fetch()) $w = (int)$weight;
    $stmt->close();
    return $w;
}

function insertRiskSignal($conn, $order_id, $user_id, $signal_type, $signal_value, $weight) {
    $stmt = $conn->prepare("INSERT INTO risk_signals (order_id, user_id, signal_type, signal_value, weight) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('iissi', $order_id, $user_id, $signal_type, $signal_value, $weight);
    $stmt->execute();
    $stmt->close();
}

function sumRiskWeight($conn, $order_id) {
    $stmt = $conn->prepare("SELECT SUM(weight) FROM risk_signals WHERE order_id=?");
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $sum = 0;
    $stmt->bind_result($sum);
    $total = 0;
    if ($stmt->fetch()) $total = (int)$sum;
    $stmt->close();
    return $total;
}

function sumRiskWeightDay($conn, $user_id) {
    $stmt = $conn->prepare("SELECT SUM(weight) FROM risk_signals WHERE user_id=? AND DATE(created_at)=CURDATE()");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $sum = 0;
    $stmt->bind_result($sum);
    $total = 0;
    if ($stmt->fetch()) $total = (int)$sum;
    $stmt->close();
    return $total;
}

function sumRiskWeightUser($conn, $user_id) {
    $stmt = $conn->prepare("SELECT SUM(weight) FROM risk_signals WHERE user_id=?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $sum = 0;
    $stmt->bind_result($sum);
    $total = 0;
    if ($stmt->fetch()) $total = (int)$sum;
    $stmt->close();
    return $total;
}

function insertFraudCase($conn, $user_id, $risk_score, $scope = 'checkout') {
    $status = 'open';
    $stmt = $conn->prepare("INSERT INTO fraud_cases (scope, user_id, risk_score, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('siss', $scope, $user_id, $risk_score, $status);
    $stmt->execute();
    $stmt->close();
}

function lockUserAccount($conn, $user_id) {
    $stmt = $conn->prepare("UPDATE users SET status=0 WHERE id=?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->close();
}

// --- MODIFIED: Insert address if not exists before update ---
function ensureAddressExists($conn, $user_id, $label = 'shipping', $line1 = '', $city = '', $state = '', $postal_code = '', $country_name = '') {
    $stmt = $conn->prepare("SELECT id FROM addresses WHERE user_id=? AND label=? LIMIT 1");
    $stmt->bind_param('is', $user_id, $label);
    $stmt->execute();
    $address_id = null;
    $stmt->bind_result($address_id);
    $exists = $stmt->fetch();
    $stmt->close();
    if (!$exists) {
        $stmt = $conn->prepare("INSERT INTO addresses (user_id, label, line1, city, state, postal_code, country) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('issssss', $user_id, $label, $line1, $city, $state, $postal_code, $country_name);
        $stmt->execute();
        $stmt->close();
    }
}

function updateAddress($conn, $user_id, $line1, $city, $state, $postal_code, $country_name) {
    // Ensure address exists before update
    ensureAddressExists($conn, $user_id, 'shipping', $line1, $city, $state, $postal_code, $country_name);
    $stmt = $conn->prepare("UPDATE addresses SET line1=?, city=?, state=?, postal_code=?, country=? WHERE user_id=? AND label='shipping'");
    $stmt->bind_param('sssssi', $line1, $city, $state, $postal_code, $country_name, $user_id);
    $stmt->execute();
    $stmt->close();
}

function getIpAddress() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return $_SERVER['HTTP_X_FORWARDED_FOR'];
    return $_SERVER['REMOTE_ADDR'] ?? '';
}

function getCountryByAddress($address) {
  $url = "https://nominatim.openstreetmap.org/search?format=json&q=" . urlencode($address);
  $opts = [
    "http" => [
      "header" => "User-Agent: TechParts/1.0\r\n"
    ]
  ];
  $context = stream_context_create($opts);
  $json = @file_get_contents($url, false, $context);
  if ($json) {
    $data = json_decode($json, true);
    if (!empty($data[0]['address']['country'])) {
      return trim($data[0]['address']['country']);
    }
  }
  return '';
}

function getCountryFromAddressesTable($conn, $user_id) {
    $stmt = $conn->prepare("SELECT country FROM addresses WHERE user_id=? AND label='shipping' LIMIT 1");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $country = '';
    $stmt->bind_result($country);
    if ($stmt->fetch()) {
        $country = trim($country);
    }
    $stmt->close();
    return $country;
}

// --- Handle Checkout POST ---
$checkoutSuccess = false;
$checkoutError = '';
$failure_reason = '';
$fraud_weights = [];
$risk_score = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
  $full_name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $address = trim($_POST['address'] ?? '');
  $country_text = trim($_POST['country'] ?? '');
  $cart_json = $_POST['cart'] ?? '';
  $card_last4 = substr(preg_replace('/\D/', '', $_POST['card'] ?? ''), -4);
  $card_brand = 'VISA';
  $cart = json_decode($cart_json, true);
  $ip_address = getIpAddress();

  // 2. Validate
  if (!$full_name || !$email || !$address || !$country_text || !is_array($cart) || !count($cart)) {
    $checkoutError = "Invalid checkout data.";
  } else {
    $conn->begin_transaction();
    try {
      // 3. Find or create user
      $stmt = $conn->prepare("SELECT id, role, status FROM users WHERE email=? LIMIT 1");
      $stmt->bind_param('s', $email);
      $stmt->execute();
      $stmt->bind_result($user_id, $role, $user_status);
      if ($stmt->fetch()) {
        $stmt->close();
      } else {
        $stmt->close();
        $password_hash = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);
        $role = 'customer';
        $user_status = 1;
        $stmt = $conn->prepare("INSERT INTO users (email, password_hash, full_name, role, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssi', $email, $password_hash, $full_name, $role, $user_status);
        $stmt->execute();
        $user_id = $stmt->insert_id;
        $stmt->close();
      }

      // Only run fraud logic for customers
      if ($role === 'customer') {
        // 4. Update address (not insert)
        $label = 'shipping';
        $line1 = $address;
        $city = $address;
        $state = $address;
        $postal_code = '';
        updateAddress($conn, $user_id, $line1, $city, $state, $postal_code, $country_text);

        // 5. Create cart
        $status_cart = 'converted';
        $stmt = $conn->prepare("INSERT INTO carts (user_id, status) VALUES (?, ?)");
        $stmt->bind_param('is', $user_id, $status_cart);
        $stmt->execute();
        $cart_id = $stmt->insert_id;
        $stmt->close();

        // 6. Insert cart_items and update stock
        $total = 0;
        foreach ($cart as $item) {
          $pid = (int)$item['id'];
          $qty = (int)$item['qty'];
          $stmt = $conn->prepare("SELECT price, stock_qty FROM products WHERE id=? LIMIT 1");
          $stmt->bind_param('i', $pid);
          $stmt->execute();
          $stmt->bind_result($price, $stock_qty);
          if ($stmt->fetch()) {
            if ($qty > $stock_qty) throw new Exception("Insufficient stock for product $pid");
            $total += $price * $qty;
          } else {
            throw new Exception("Product not found: $pid");
          }
          $stmt->close();

          $stmt = $conn->prepare("INSERT INTO cart_items (cart_id, product_id, qty, price_each) VALUES (?, ?, ?, ?)");
          $stmt->bind_param('iiii', $cart_id, $pid, $qty, $price);
          $stmt->execute();
          $stmt->close();

          $stmt = $conn->prepare("UPDATE products SET stock_qty = stock_qty - ? WHERE id=?");
          $stmt->bind_param('ii', $qty, $pid);
          $stmt->execute();
          $stmt->close();
        }

        // 7. Get address_id for user
        $stmt = $conn->prepare("SELECT id FROM addresses WHERE user_id=? AND label='shipping' LIMIT 1");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $address_id = null;
        $stmt->bind_result($address_id);
        if (!$stmt->fetch()) throw new Exception("Address not found for user.");
        $stmt->close();

        // 8. Create order
        $status_order = 'paid';
        $stmt = $conn->prepare("INSERT INTO orders (cart_id, user_id, address_id, total, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('iiiis', $cart_id, $user_id, $address_id, $total, $status_order);
        $stmt->execute();
        $order_id = $stmt->insert_id;
        $stmt->close();

        // --- FRAUD DETECTION LOGIC ---
        // 1. Compare country from addresses table with country from Nominatim API
        $db_country = getCountryFromAddressesTable($conn, $user_id);
        $api_country = getCountryByAddress($address);
        if ($db_country && $api_country && strcasecmp($db_country, $api_country) !== 0) {
          $failure_reason = 'ip_mismatch';
          $weight = fetchFraudWeight($conn, $failure_reason, 'checkout');
          insertRiskSignal($conn, $order_id, $user_id, $failure_reason, "$db_country vs $api_country", $weight);
          $fraud_weights[] = $weight;
        }

        // 2. High ticket
        if ($total > 500000) {
          $failure_reason = 'high_ticket';
          $weight = fetchFraudWeight($conn, $failure_reason, 'checkout');
          insertRiskSignal($conn, $order_id, $user_id, $failure_reason, "₦$total", $weight);
          $fraud_weights[] = $weight;
        }

        // 3. High risk country
        if (strcasecmp($country_text, 'Nigeria') === 0) {
          $failure_reason = 'high_risk_country';
          $weight = fetchFraudWeight($conn, $failure_reason, 'checkout');
          insertRiskSignal($conn, $order_id, $user_id, $failure_reason, $country_text, $weight);
          $fraud_weights[] = $weight;
        }

        // Sum all weights for this order
        $risk_score = sumRiskWeight($conn, $order_id);

                // --- MODIFIED FRAUD LOGIC: Calculate total_weight for user *for today only* and handle blacklist/error ---
        $total_weight = 0;
        $signal_types = [];
        $stmt = $conn->prepare("SELECT signal_type, SUM(weight) 
                                FROM risk_signals 
                                WHERE user_id=? AND DATE(created_at)=CURDATE()
                                GROUP BY signal_type");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $stmt->bind_result($signal_type, $weight_sum);
        while ($stmt->fetch()) {
            $total_weight += (int)$weight_sum;
            $signal_types[] = $signal_type;
        }
        $stmt->close();

        if ($total_weight > 30) {
            lockUserAccount($conn, $user_id);
            insertFraudCase($conn, $user_id, $total_weight, 'checkout');
            $checkoutError = "Checkout blocked due to suspicious activity. Signals: " . implode(', ', $signal_types) . ". Please Email technical Team.";
            $conn->commit();
            goto end_checkout;
        }

        // If risk_score > 30, lock account and insert fraud case (old logic, maintained)
        if ($risk_score > 30) {
          lockUserAccount($conn, $user_id);
          insertFraudCase($conn, $user_id, $risk_score);
          $checkoutError = "";
        }

        // Sum all weights for user in a day
        $total_weight_day = sumRiskWeightDay($conn, $user_id);
        if ($total_weight_day > 30) {
          insertFraudCase($conn, $user_id, $total_weight_day, 'daily');
          lockUserAccount($conn, $user_id);
        }

        // 9. Insert payment
        $provider = 'Demo';
        $provider_txn_id = uniqid('demo_', true);
        $status_payment = 'captured';
        $stmt = $conn->prepare("INSERT INTO payments (order_id, provider, provider_txn_id, amount, status, card_last4, card_brand) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ississs', $order_id, $provider, $provider_txn_id, $total, $status_payment, $card_last4, $card_brand);
        $stmt->execute();
        $payment_id = $stmt->insert_id;
        $stmt->close();

        // Update address with billing form data
        updateAddress($conn, $user_id, $address, $city, $state, $postal_code, $country_text);

        $conn->commit();
        $checkoutSuccess = true;

        // Activity log
        $operation = "order a product on : " . date('Y-m-d H:i:s');
        log_activity($conn, $user_id, $role, $operation, $ip_address);

        // 10. Send confirmation email
        $subject = "Your TechParts Order Confirmation";
        $itemsHtml = '';
        foreach ($cart as $item) {
          $itemsHtml .= "<tr>
            <td style='padding:8px 0;'>{$item['name']} x{$item['qty']}</td>
            <td style='padding:8px 0;text-align:right;'>₦" . number_format($item['price'] * $item['qty'], 2) . "</td>
          </tr>";
        }
        $message = '
        <html>
        <head>
          <meta charset="UTF-8">
          <title>Order Confirmation</title>
          <style>
            body { background: #f6f9fc; margin: 0; padding: 0; font-family: "Segoe UI", Arial, sans-serif; }
            .container { background: #fff; max-width: 540px; margin: 40px auto; border-radius: 12px; box-shadow: 0 4px 24px rgba(44, 62, 80, 0.08); padding: 36px 32px; }
            .header { text-align: center; margin-bottom: 32px; }
            .header h1 { color: #2d8cf0; font-size: 2.1em; margin: 0 0 10px; }
            .header img { width: 60px; margin-bottom: 10px; }
            .order-table { width:100%; border-collapse:collapse; margin:24px 0; }
            .order-table td { border-bottom:1px solid #f1f1f1; }
            .order-total { font-weight: bold; }
            .footer { margin-top: 36px; font-size: 13px; color: #888; text-align: center; }
            .btn { display: inline-block; background: #2d8cf0; color: #fff; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-weight: bold; margin-top: 18px; }
          </style>
        </head>
        <body>
          <div class="container">
            <div class="header">
              <img src="https://img.icons8.com/color/96/000000/shopping-cart.png" alt="Order Icon"/>
              <h1>Thank you for your order!</h1>
              <p style="color:#555;font-size:1.1em;">Hi <strong>' . htmlspecialchars($full_name) . '</strong>, your order has been received and is being processed.</p>
            </div>
            <table class="order-table">
              ' . $itemsHtml . '
              <tr class="order-total">
                <td>Total</td>
                <td style="text-align:right;">₦' . number_format($total, 2) . '</td>
              </tr>
            </table>
            <div>
              <p><strong>Shipping Address:</strong><br>' . htmlspecialchars($address) . '</p>
              <p><strong>Country:</strong> ' . htmlspecialchars($country_text) . '</p>
              <p><strong>Order Reference:</strong> ' . htmlspecialchars($provider_txn_id) . '</p>
              <p><strong>Payment:</strong> Card ending in ' . htmlspecialchars($card_last4) . '</p>
            </div>
            <div style="margin-top:24px;">
              <a href="#" class="btn">View My Orders</a>
            </div>
            <div class="footer">
              This is an automated message. Please do not reply.<br>
              &copy; ' . date('Y') . ' TechParts | All rights reserved.
            </div>
          </div>
        </body>
        </html>';
        if (!sendEmail($email, $subject, $message)) {
          $checkoutError = "Order placed, but failed to send confirmation email. Please contact support.";
        }
      } else {
        // Not a customer, normal checkout
        // ... (repeat address/cart/order/payment logic as above, but skip fraud logic)
      }
      end_checkout:
      // End of checkout logic
    } catch (Exception $ex) {
      $conn->rollback();
      $checkoutError = "Checkout failed: " . $ex->getMessage();
    }
  }
}

// --- Handle Login Failure (example) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $conn->prepare("SELECT id, password_hash FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->bind_result($user_id, $password_hash);
    if ($stmt->fetch()) {
        if (!password_verify($password, $password_hash)) {
            // Login failure
            $risk_score = 0;
            $scope = 'login';
            $status = 'open';
            $stmt2 = $conn->prepare("INSERT INTO fraud_cases (scope, user_id, risk_score, status) VALUES (?, ?, ?, ?)");
            $stmt2->bind_param('siss', $scope, $user_id, $risk_score, $status);
            $stmt2->execute();
            $stmt2->close();
        }
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>TechParts – Premium Computer Components</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Upgrade your PC with premium computer parts. Shop CPUs, GPUs, RAM, SSDs and more. Fast shipping, secure checkout, and expert support.">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="assets/css/styles.css?v=1.0">
  <link rel="shortcut icon" href="assets/images/logo.png" type="image/x-icon" />
</head>
<body>
  <!-- Header -->
  <header class="header" id="header">
    <?php include('partials/navbar.php'); ?>
  </header>

  <?php if ($checkoutSuccess): ?>
    <div style="background:#e6ffed;color:#155724;padding:24px;text-align:center;margin:24px auto;max-width:600px;border-radius:8px;">
      <h2>Thank you for your purchase!</h2>
      <p>Your order has been placed and a confirmation email sent to you.</p>
    </div>
  <?php elseif ($checkoutError): ?>
    <div style="background:#ffe6e6;color:#721c24;padding:24px;text-align:center;margin:24px auto;max-width:600px;border-radius:8px;">
      <h2>Checkout Error</h2>
      <p><?php echo htmlspecialchars($checkoutError); ?></p>
    </div>
  <?php endif; ?>

  <!-- Hero -->
  <section class="hero" aria-labelledby="heroTitle">
    <div class="hero-content container">
      <div class="hero-text">
        <h1 id="heroTitle">Upgrade Your PC with Premium Parts</h1>
        <p class="hero-tagline">Discover the best deals on CPUs, GPUs, RAM, SSDs, and more. Fast shipping, secure checkout, and expert support for all your computer hardware needs.</p>
        <button class="cta-btn" onclick="document.getElementById('products').scrollIntoView({behavior:'smooth'})">
          Shop Now <i class="fas fa-arrow-right"></i>
        </button>
      </div>
      <div class="hero-image">
        <img src="https://unsplash.it/600/400?image=1062" alt="Modern computer hardware on desk" />
      </div>
    </div>
  </section>

  <!-- Products -->
  <main>
    <section class="products-section container" id="products" aria-labelledby="productsTitle">
      <h2 class="section-title" id="productsTitle">Featured Products</h2>
      <div class="search-bar" id="searchBar" style="display:none;">
        <input type="text" id="searchInput" placeholder="Search products..." aria-label="Search products" autocomplete="off">
      </div>
      <div class="products-grid" id="productsGrid" aria-live="polite">
        <?php if (count($products)): ?>
          <?php foreach ($products as $product): ?>
            <div class="product-card" data-name="<?php echo htmlspecialchars($product['name']); ?>" data-desc="<?php echo htmlspecialchars($product['description']); ?>">
              <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?> photo" loading="lazy">
              <div class="product-info">
                <div class="product-title"><?php echo htmlspecialchars($product['name']); ?></div>
                <div class="product-desc"><?php echo $product['description'] ? htmlspecialchars($product['description']) : 'N/A'; ?></div>
                <div class="product-price">₦<?php echo number_format($product['price'], 2); ?></div>
                <div class="product-stock">
                  <?php if ((int)$product['stock_qty'] > 0): ?>
                    <span style="color:green;">Quantity remaining: <?php echo (int)$product['stock_qty']; ?></span>
                  <?php else: ?>
                    <span style="color:red;">Out of stock</span>
                  <?php endif; ?>
                </div>
                <button class="add-cart-btn" data-id="<?php echo $product['id']; ?>" <?php if ((int)$product['stock_qty'] <= 0) echo 'disabled style="opacity:0.5;cursor:not-allowed;"'; ?>>
                  <i class="fas fa-cart-plus"></i> Add to Cart
                </button>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div style="text-align:center;color:#888;">No products available.</div>
        <?php endif; ?>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <footer class="footer">
    <div class="container footer-content">
      <div class="footer-brand">
        <span class="logo"><i class="fas fa-microchip"></i> TechParts</span>
        <p>Premium computer components for enthusiasts and professionals.</p>
      </div>
      <form class="newsletter-form" id="newsletterForm" autocomplete="off" aria-label="Subscribe to newsletter">
        <label for="newsletterEmail" class="visually-hidden">Email address</label>
        <input type="email" id="newsletterEmail" placeholder="Your email" required aria-required="true">
        <button type="submit">Subscribe</button>
      </form>
      <div class="footer-links">
        <a href="#products">Products</a>
        <a href="#">Support</a>
        <a href="#">Contact</a>
        <a href="#">Terms</a>
      </div>
    </div>
    <div class="footer-bottom">
      <?php  include('partials/footer.php') ?> 
    </div>
  </footer>

  <!-- Modals -->
  <div class="modal" id="cartModal" role="dialog" aria-modal="true" aria-labelledby="cartTitle" tabindex="-1">
    <div class="modal-content cart-modal-content">
      <button class="close-modal" aria-label="Close" data-close="cartModal">&times;</button>
      <h2 id="cartTitle">Your Cart</h2>
      <div class="cart-items" id="cartItems"></div>
      <div class="cart-summary">
        <div class="cart-total" id="cartTotal">Total: ₦0.00</div>
        <button class="checkout-btn" id="checkoutBtn">Checkout</button>
      </div>
    </div>
  </div>

  <div class="modal" id="checkoutModal" role="dialog" aria-modal="true" aria-labelledby="checkoutTitle" tabindex="-1">
    <div class="modal-content checkout-modal-content">
      <button class="close-modal" aria-label="Close" data-close="checkoutModal">&times;</button>
      <h2 id="checkoutTitle">Checkout</h2>
      <div class="checkout-wrapper">
        <form id="checkoutForm" method="POST" novalidate>
          <input type="hidden" name="checkout" value="1">
          <input type="hidden" name="cart" id="cartField">
          <div class="form-group">
            <label for="checkoutName">Full Name</label>
            <input type="text" id="checkoutName" name="name" required autocomplete="name" aria-required="true" placeholder="Your full name">
            <span class="form-error" id="checkoutNameError"></span>
          </div>
          <div class="form-group">
            <label for="checkoutEmail">Email</label>
            <input type="email" id="checkoutEmail" name="email" required autocomplete="email" aria-required="true" placeholder="you@email.com">
            <span class="form-error" id="checkoutEmailError"></span>
          </div>
          <div class="form-group">
            <label for="checkoutAddress">Shipping Address</label>
            <input type="text" id="checkoutAddress" name="address" required autocomplete="shipping street-address" aria-required="true" placeholder="123 Main St, City">
            <span class="form-error" id="checkoutAddressError"></span>
          </div>
          <div class="form-group">
            <label for="checkoutCountry">Country</label>
            <input type="text" id="checkoutCountry" name="country" required maxlength="56" autocomplete="country" aria-required="true" placeholder="Country Name">
            <span class="form-error" id="checkoutCountryError"></span>
          </div>
          <div class="form-group">
            <label for="checkoutCard">Card Number</label>
            <input type="text" id="checkoutCard" name="card" required pattern="\d{16}" maxlength="16" inputmode="numeric" aria-required="true" placeholder="1234 5678 9012 3456">
            <span class="form-error" id="checkoutCardError"></span>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="checkoutExpiry">Expiry</label>
              <input type="text" id="checkoutExpiry" name="expiry" required pattern="\d{2}/\d{2}" maxlength="5" inputmode="numeric" aria-required="true" placeholder="MM/YY">
              <span class="form-error" id="checkoutExpiryError"></span>
            </div>
            <div class="form-group">
              <label for="checkoutCVC">CVC</label>
              <input type="text" id="checkoutCVC" name="cvc" required pattern="\d{3}" maxlength="3" inputmode="numeric" aria-required="true" placeholder="123">
              <span class="form-error" id="checkoutCVCError"></span>
            </div>
          </div>
          <button type="submit" class="modal-btn">Pay Now</button>
        </form>
        <aside class="order-summary" id="orderSummary">
          <!-- Order summary will be rendered here -->
        </aside>
      </div>
    </div>
  </div>

  <div class="modal" id="confirmationModal" role="dialog" aria-modal="true" aria-labelledby="confirmationTitle" tabindex="-1">
    <div class="modal-content confirmation-modal-content">
      <button class="close-modal" aria-label="Close" data-close="confirmationModal">&times;</button>
      <h2 id="confirmationTitle">Payment Processed</h2>
      <div class="confirmation-message">
        <i class="fas fa-check-circle"></i>
        <p>Your order has been placed successfully!<br>Thank you for shopping with TechParts.</p>
      </div>
    </div>
  </div>

  <div class="toast" id="toast" role="status" aria-live="polite"></div>

  <script>
    // Responsive nav
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');
    if (navToggle && navMenu) {
      navToggle.onclick = () => {
        navMenu.classList.toggle('open');
        navToggle.setAttribute('aria-expanded', navMenu.classList.contains('open'));
      };
      document.body.addEventListener('click', e => {
        if (!navMenu.contains(e.target) && !navToggle.contains(e.target)) navMenu.classList.remove('open');
      });
    }

    // Dark mode
    const darkToggle = document.getElementById('darkToggle');
    if (darkToggle) {
      darkToggle.onclick = () => {
        document.documentElement.toggleAttribute('data-theme', 'dark');
        darkToggle.innerHTML = document.documentElement.hasAttribute('data-theme') ?
          '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
      };
    }

    // Cart logic
    let cart = [];
    function updateCartCount() {
      const cartCount = document.getElementById('cartCount');
      if (cartCount) {
        cartCount.textContent = cart.reduce((a, c) => a + c.qty, 0);
      }
    }
    function showToast(msg) {
      const toast = document.getElementById('toast');
      toast.textContent = msg;
      toast.classList.add('show');
      setTimeout(() => toast.classList.remove('show'), 2000);
    }
    // Add to cart logic
    document.getElementById('productsGrid').addEventListener('click', e => {
      const btn = e.target.closest('.add-cart-btn');
      if (btn && !btn.disabled) {
        const card = btn.closest('.product-card');
        const id = +btn.dataset.id;
        const name = card.querySelector('.product-title').textContent;
        const price = parseFloat(card.querySelector('.product-price').textContent.replace(/[^\d.]/g, ''));
        const image = card.querySelector('img').src;
        const desc = card.querySelector('.product-desc').textContent;
        const stock = parseInt(card.querySelector('.product-stock').textContent.replace(/\D/g,''));
        let item = cart.find(i => i.id === id);
        if (item) {
          if (item.qty < stock) {
            item.qty++;
            showToast('Added to cart');
          } else {
            showToast('No more stock available');
          }
        } else {
          cart.push({id, name, price, image, description: desc, qty: 1, stock});
          showToast('Added to cart');
        }
        updateCartCount();
      }
    });

    // Cart modal
    const cartBtn = document.getElementById('cartBtn');
    const cartModal = document.getElementById('cartModal');
    const cartItems = document.getElementById('cartItems');
    const cartTotal = document.getElementById('cartTotal');
    if (cartBtn) {
      cartBtn.onclick = () => { openModal('cartModal'); renderCart(); };
    }
    function renderCart() {
      if (!cartItems || !cartTotal) return;
      cartItems.innerHTML = '';
      if (!cart.length) {
        cartItems.innerHTML = '<div style="text-align:center;color:#888;">Your cart is empty.</div>';
        cartTotal.textContent = 'Total: ₦0.00';
        return;
      }
      cart.forEach(item => {
        const div = document.createElement('div');
        div.className = 'cart-item';
        div.innerHTML = `
          <img src="${item.image}" alt="${item.name}" class="cart-item-img">
          <div class="cart-item-info">
            <div class="cart-item-title">${item.name}</div>
            <div class="cart-item-qty">x${item.qty}</div>
          </div>
          <div class="cart-item-price">₦${(item.price * item.qty).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>
          <button class="cart-item-remove" data-id="${item.id}" aria-label="Remove"><i class="fas fa-trash"></i></button>
        `;
        cartItems.appendChild(div);
      });
      cartTotal.textContent = 'Total: ₦' + cart.reduce((a, c) => a + c.price * c.qty, 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
    if (cartItems) {
      cartItems.addEventListener('click', e => {
        if (e.target.closest('.cart-item-remove')) {
          const id = +e.target.closest('.cart-item-remove').dataset.id;
          cart = cart.filter(i => i.id !== id);
          updateCartCount();
          renderCart();
        }
      });
    }

    // Checkout
    const checkoutBtn = document.getElementById('checkoutBtn');
    if (checkoutBtn) {
      checkoutBtn.onclick = () => {
        if (!cart.length) return;
        openModal('checkoutModal');
        renderOrderSummary();
      };
    }
    function renderOrderSummary() {
      const orderSummary = document.getElementById('orderSummary');
      if (!orderSummary) return;
      if (!cart.length) { orderSummary.innerHTML = ''; return; }
      orderSummary.innerHTML = `
        <h3 style="margin-top:0;">Order Summary</h3>
        <ul style="list-style:none;padding:0;margin:0 0 1em 0;">
          ${cart.map(item => `<li>${item.name} x${item.qty} <span style="float:right;">₦${(item.price*item.qty).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span></li>`).join('')}
        </ul>
        <div style="font-weight:700;">Total: ₦${cart.reduce((a, c) => a + c.price * c.qty, 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>
      `;
    }

    // Modal logic
    function openModal(id) {
      document.querySelectorAll('.modal').forEach(m => m.classList.remove('active'));
      document.getElementById(id).classList.add('active');
      document.body.style.overflow = 'hidden';
      setTimeout(() => {
        const firstInput = document.getElementById(id).querySelector('input,button,select,textarea');
        if (firstInput) firstInput.focus();
      }, 100);
    }
    function closeModalAll() {
      document.querySelectorAll('.modal').forEach(m => m.classList.remove('active'));
      document.body.style.overflow = '';
    }
    document.querySelectorAll('.close-modal').forEach(btn => {
      btn.onclick = () => closeModalAll();
    });
    window.addEventListener('keydown', e => { if (e.key === 'Escape') closeModalAll(); });

    // Checkout validation
    function validateEmail(email) {
      return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }
    const checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
      checkoutForm.onsubmit = function(e) {
        e.preventDefault();
        let valid = true;
        const name = this.checkoutName.value.trim();
        const email = this.checkoutEmail.value.trim();
        const addr = this.checkoutAddress.value.trim();
        const country = this.checkoutCountry.value.trim();
        const card = this.checkoutCard.value.replace(/\s/g, '');
        const expiry = this.checkoutExpiry.value.trim();
        const cvc = this.checkoutCVC.value.trim();
        ['checkoutName','checkoutEmail','checkoutAddress','checkoutCountry','checkoutCard','checkoutExpiry','checkoutCVC'].forEach(id => {
          document.getElementById(id+'Error').textContent = '';
        });
        if (!name) { document.getElementById('checkoutNameError').textContent = 'Enter your name.'; valid = false; }
        if (!validateEmail(email)) { document.getElementById('checkoutEmailError').textContent = 'Enter a valid email.'; valid = false; }
        if (!addr) { document.getElementById('checkoutAddressError').textContent = 'Enter your address.'; valid = false; }
        if (!country) { document.getElementById('checkoutCountryError').textContent = 'Enter country name.'; valid = false; }
        if (!/^\d{16}$/.test(card)) { document.getElementById('checkoutCardError').textContent = 'Card must be 16 digits.'; valid = false; }
        if (!/^\d{2}\/\d{2}$/.test(expiry)) { document.getElementById('checkoutExpiryError').textContent = 'Format MM/YY.'; valid = false; }
        if (!/^\d{3}$/.test(cvc)) { document.getElementById('checkoutCVCError').textContent = 'CVC must be 3 digits.'; valid = false; }
        if (valid) {
          // Set cart JSON for PHP
          document.getElementById('cartField').value = JSON.stringify(cart.map(i => ({
            id: i.id, name: i.name, price: i.price, qty: i.qty
          })));
          this.submit();
        }
      };
    }

    // Newsletter
    const newsletterForm = document.getElementById('newsletterForm');
    if (newsletterForm) {
      newsletterForm.onsubmit = function(e) {
        e.preventDefault();
        const email = this.newsletterEmail.value.trim();
        if (!validateEmail(email)) {
          this.newsletterEmail.style.borderColor = '#e11d48';
          showToast('Enter a valid email.');
          return;
        }
        this.newsletterEmail.style.borderColor = '';
        showToast('Subscribed! (demo)');
        this.reset();
      };
    }

    // Search bar
    const searchBtn = document.getElementById('searchBtn');
    const searchBar = document.getElementById('searchBar');
    let searchVisible = false;
    if (searchBtn && searchBar) {
      searchBtn.onclick = () => {
        searchVisible = !searchVisible;
        searchBar.style.display = searchVisible ? 'block' : 'none';
        if (searchVisible) document.getElementById('searchInput').focus();
      };
    }
    let searchTimeout;
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
      searchInput.oninput = function() {
        clearTimeout(searchTimeout);
        const val = this.value.trim().toLowerCase();
        searchTimeout = setTimeout(() => {
          document.querySelectorAll('.product-card').forEach(card => {
            const name = card.dataset.name.toLowerCase();
            const desc = card.dataset.desc.toLowerCase();
            card.style.display = (name.includes(val) || desc.includes(val)) ? '' : 'none';
          });
        }, 250);
      };
    }

    // Reveal animations
    function revealOnScroll() {
      const cards = document.querySelectorAll('.product-card');
      if (!('IntersectionObserver' in window)) return;
      const observer = new window.IntersectionObserver(entries => {
        entries.forEach(entry => {
          if (entry.isIntersecting) entry.target.classList.add('visible');
        });
      }, { threshold: 0.15 });
      cards.forEach(card => observer.observe(card));
    }
    revealOnScroll();

    // Initial
    updateCartCount();
  </script>
</body>
</html>
