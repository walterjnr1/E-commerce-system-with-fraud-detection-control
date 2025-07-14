<?php
include('config.php');

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
}

$success = '';
$error = '';

// Fetch user and address info
$stmt = $conn->prepare(" SELECT u.email, u.full_name, u.phone, a.id as address_id, a.label, a.line1, a.city, a.state, a.postal_code, a.country
    FROM users u LEFT JOIN addresses a ON u.id = a.user_id AND a.label = 'shipping' WHERE u.id = ? LIMIT 1
");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $line1 = trim($_POST['line1'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $postal_code = trim($_POST['postal_code'] ?? '');
    $country = strtoupper(trim($_POST['country'] ?? ''));

  
        // Update user
        $stmt = $conn->prepare("UPDATE users SET full_name=?, phone=? WHERE id=?");
        $stmt->bind_param('ssi', $full_name, $phone, $user_id);
        $stmt->execute();

        // Update or insert address
        if ($user['address_id']) {
            $stmt = $conn->prepare("UPDATE addresses SET line1=?, city=?, state=?, postal_code=?, country=? WHERE id=?");
            $stmt->bind_param('sssssi', $line1, $city, $state, $postal_code, $country, $user['address_id']);
            $stmt->execute();

           // Activity log
        $operation = "Updated his profile on: " . date('Y-m-d H:i:s');
        log_activity($conn, $user_id, $role, $operation, $ip_address);

        } else {
            $stmt = $conn->prepare("INSERT INTO addresses (user_id, label, line1, city, state, postal_code, country_iso2) VALUES (?, 'shipping', ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('isssss', $user_id, $line1, $city, $state, $postal_code, $country_iso2);
            $stmt->execute();
        }

        $success = "Profile updated successfully.";
        header("Location: profile.php?success=1");
    }


if (isset($_GET['success'])) {
    $success = "Profile updated successfully.";
}

// Refresh user/address info after update
$stmt = $conn->prepare("
    SELECT u.email, u.full_name, u.phone, a.id as address_id, a.label, a.line1, a.city, a.state, a.postal_code, a.country
    FROM users u
    LEFT JOIN addresses a ON u.id = a.user_id AND a.label = 'shipping'
    WHERE u.id = ?
    LIMIT 1
");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Profile – TechParts</title>
<?php include('partials/head.php');?>
  <link rel="stylesheet" href="assets/css/profile.css">
</head>
<body>
  <!-- Header -->
      <header class="header" id="header">
  <?php include('partials/navbar.php'); ?>
  </header>

  <main>
    <section class="profile-section container" id="profile">
      <h2>Your Profile</h2>
     



<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

      <form method="post" autocomplete="off" novalidate>
        <div class="form-group">
          <label for="email">Email (cannot change)</label>
          <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
        </div>
        <div class="form-group">
          <label for="full_name">Full Name <span style="color:#e11d48">*</span></label>
          <input type="text" id="full_name" name="full_name" required value="<?php echo htmlspecialchars($user['full_name']); ?>">
        </div>
        <div class="form-group">
          <label for="phone">Phone</label>
          <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
        </div>
        <hr style="margin:2em 0 1.5em 0;border:0;border-top:1.5px solid var(--border);">
        <div class="form-group">
          <label for="line1">Address Line 1 <span style="color:#e11d48">*</span></label>
          <input type="text" id="line1" name="line1" required value="<?php echo htmlspecialchars($user['line1'] ?? ''); ?>">
        </div>
       
        <div class="form-group">
          <label for="city">City <span style="color:#e11d48">*</span></label>
          <input type="text" id="city" name="city" required value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>">
        </div>
        <div class="form-group">
          <label for="state">State/Province</label>
          <input type="text" id="state" name="state" value="<?php echo htmlspecialchars($user['state'] ?? ''); ?>">
        </div>
        <div class="form-group">
          <label for="postal_code">Postal Code</label>
          <input type="text" id="postal_code" name="postal_code" value="<?php echo htmlspecialchars($user['postal_code'] ?? ''); ?>">
        </div>
        <div class="form-group">
          <label for="country_iso2">Country <span style="color:#e11d48">*</span></label>
          <input type="text" id="country" name="country" maxlength="2" required style="text-transform:uppercase"
            value="<?php echo htmlspecialchars($user['country'] ?? ''); ?>">
        </div>
        <button type="submit" class="form-btn"><i class="fas fa-save"></i> Update Profile</button>
      </form>
    </section>
  </main>

  <footer class="footer">
    <div class="container footer-content">
      <div class="footer-brand">
        <span class="logo"><i class="fas fa-microchip"></i> TechParts</span>
        <p>Premium computer components for enthusiasts and professionals.</p>
      </div>
      <div class="footer-links">
        <a href="index.php#products">Products</a>
        <a href="#">Support</a>
        <a href="#">Contact</a>
        <a href="#">Terms</a>
      </div>
    </div>
    <div class="footer-bottom">
      <?php include('partials/footer.php') ?>
    </div>
  </footer>
</body>
</html>
