<?php
include('config.php');

$error = '';
$successMsg = '';

function get_client_ip() {
  $ip_keys = [
    'HTTP_CLIENT_IP',
    'HTTP_X_FORWARDED_FOR',
    'HTTP_X_FORWARDED',
    'HTTP_X_CLUSTER_CLIENT_IP',
    'HTTP_FORWARDED_FOR',
    'HTTP_FORWARDED',
    'REMOTE_ADDR'
  ];
  foreach ($ip_keys as $key) {
    if (!empty($_SERVER[$key])) {
      $ip_list = explode(',', $_SERVER[$key]);
      foreach ($ip_list as $ip) {
        $ip = trim($ip);
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
          return $ip;
        }
      }
    }
  }
  return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

// Get country by IP (simple API, you may want to cache or use a better service)
function get_country_by_ip($ip) {
  $country = '';
  $url = "https://ipapi.co/$ip/country_name/";
  $context = stream_context_create(['http' => ['timeout' => 2]]);
  $result = @file_get_contents($url, false, $context);
  if ($result !== false) {
    $country = trim($result);
  }
  return $country;
}

function insert_fraud_case($conn, $scope, $user_id, $risk_score) {
  $stmt = $conn->prepare("INSERT INTO fraud_cases (scope, user_id, risk_score, status) VALUES (?, ?, ?, 'open')");
  $stmt->bind_param("sii", $scope, $user_id, $risk_score);
  $stmt->execute();
  $stmt->close();
}

// Calculate total weight based on failure_reason in login_attempts table
function calculate_total_weight($conn, $user_id) {
  $total_weight = 0;
  $stmt = $conn->prepare(
    "SELECT la.failure_reason, fr.weight 
     FROM login_attempts la 
     JOIN fraud_rules fr ON la.failure_reason = fr.signal_type 
     WHERE la.user_id = ? AND fr.is_active = 1"
  );
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  while ($row = $result->fetch_assoc()) {
    $total_weight += (int)$row['weight'];
  }
  $stmt->close();
  return $total_weight;
}

if (isset($_POST['btnlogin'])) {
  $email = trim($_POST['txtemail']);
  $password = $_POST['txtpassword'];
  $ip_address = get_client_ip();
  $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
  $user_id = null;
  $login_success = false;
  $failure_reason = null;
  $frequent_failure = false;
  $scope = 'login';

  // Check country by IP
  $country = get_country_by_ip($ip_address);
  $is_nigeria = (strtolower($country) === 'nigeria');

  // Find user by email and active status
  $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($user = $result->fetch_assoc()) {
    $user_id = $user['id'];
    $role = $user['role'];
    $status = $user['status'];

    // If user is blacklisted
    if ($status == 0) {
      $error = "User account has been blacklisted.";
    } else {
      // Check for frequent login failures in last 1 minute (for customers only)
      if ($role === 'customer') {
        $stmt_fail = $conn->prepare(
          "SELECT COUNT(*) as fail_count FROM login_attempts 
           WHERE user_id = ? AND success = 0 AND created_at >= (NOW() - INTERVAL 1 MINUTE)"
        );
        $stmt_fail->bind_param("i", $user_id);
        $stmt_fail->execute();
        $fail_result = $stmt_fail->get_result();
        $fail_row = $fail_result->fetch_assoc();
        $fail_count = $fail_row['fail_count'] ?? 0;
        $stmt_fail->close();

        if ($fail_count >= 1) { // At least one previous failure in 1 minute
          $frequent_failure = true;
          $failure_reason = 'frequent_login_failures';
        }
      }

      if (password_verify($password, $user['password_hash'])) {
        $login_success = true;

        // Activity log (optional, as in your original code)
        $operation = "logged in as $role on: " . date('Y-m-d H:i:s');
        if (function_exists('log_activity')) {
          log_activity($conn, $user_id, $role, $operation, $ip_address);
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['fullname'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['logged'] = time();

        // Log successful login attempt
        $stmt_log = $conn->prepare("INSERT INTO login_attempts (user_id, email_submitted, user_agent, ip_address, success, failure_reason) VALUES (?, ?, ?, ?, 1, NULL)");
        $stmt_log->bind_param("isss", $user_id, $email, $user_agent, $ip_address);
        $stmt_log->execute();
        $stmt_log->close();

        if ($user['role'] === 'customer') {
          header("Location: index.php");
          exit;
        } elseif ($user['role'] === 'admin') {
          header("Location: Admin/index.php");
          exit;
        } else {
          $failure_reason = 'Unknown_user_role';
          $error = "Unknown user role.";
        }
      } else {
        // Signals for fraud rules
        if ($frequent_failure) {
          $failure_reason = 'frequent_login_failures';
        } else {
          $failure_reason = 'bad_password';
        }
        if ($is_nigeria) {
          $failure_reason = 'high_risk_country';
        }

        $error = $frequent_failure
          ? "Multiple login failures detected. Your account may be locked for security reasons."
          : "Invalid password!";

        // Log failed login attempt
        $stmt_log = $conn->prepare("INSERT INTO login_attempts (user_id, email_submitted, user_agent, ip_address, success, failure_reason) VALUES (?, ?, ?, ?, 0, ?)");
        $stmt_log->bind_param("issss", $user_id, $email, $user_agent, $ip_address, $failure_reason);
        $stmt_log->execute();
        $stmt_log->close();

        // Fraud detection logic for customers on failed login
        if ($role === 'customer') {
          $total_weight = calculate_total_weight($conn, $user_id);

          // Insert fraud case only if total weight > 79
          if ($total_weight > 79) {
            insert_fraud_case($conn, $scope, $user_id, $total_weight);
          }

          // Blacklist account if total weight > 79
          if ($total_weight > 79) {
            $stmt_lock = $conn->prepare("UPDATE users SET status = 0 WHERE id = ?");
            $stmt_lock->bind_param("i", $user_id);
            $stmt_lock->execute();
            $stmt_lock->close();
            $error = "User account has been blacklisted. Contact Admin";
          }
        }
      }
    }
  } else {
    $failure_reason = 'no_user_or_inactive';
    $error = "No user found with this email or account is inactive.";

    // Log failed login attempt with user_id as NULL
    $stmt_log = $conn->prepare("INSERT INTO login_attempts (user_id, email_submitted, user_agent, ip_address, success, failure_reason) VALUES (?, ?, ?, ?, 0, ?)");
    $null_user_id = null;
    $stmt_log->bind_param("issss", $null_user_id, $email, $user_agent, $ip_address, $failure_reason);
    $stmt_log->execute();
    $stmt_log->close();
  }

  $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - TechParts</title>
  <?php include('partials/head.php'); ?>
  <link rel="stylesheet" href="assets/css/signup_style.css">
</head>
<body>
  <!-- Header -->
   <header class="header" id="header">
  <?php include('partials/navbar.php'); ?>
  </header>

  <!-- Login section -->
  <main class="signup-section">
    <div class="signup-card" role="form" aria-labelledby="loginTitle">
      <h1 id="loginTitle">Login to Your Account</h1>
       <?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($successMsg): ?>
    <div class="alert alert-success"><?php echo $successMsg; ?></div>
    <?php endif; ?>
    <form  method="post"  >
        <div class="form-group">
          <label for="txtemail">Email</label>
          <input type="email" id="txtemail" name="txtemail" autocomplete="email" required placeholder="you@email.com" aria-required="true">
        </div>
        <div class="form-group">
          <label for="txtpassword">Password</label>
          <div class="password-field">
            <input type="password" id="txtpassword" name="txtpassword" autocomplete="current-password" required placeholder="Your password" aria-required="true">
            <button type="button" class="toggle-password" aria-label="Show password"><i class="fas fa-eye"></i></button>
          </div>
        </div>
        <button type="submit" name="btnlogin" class="submit-btn">Login</button>
        <div class="auth-extra">Don't have an account? <a href="signup.php">Sign up</a></div>
      </form>
    </div>
  </main>

  <!-- Footer -->
  <footer class="footer">
<?php  include('partials/footer.php') ?> 
 </footer>

  <!-- Toast notification -->
  <div class="toast" id="toast" role="status" aria-live="polite"></div>

  <script>
    // Dark‑mode toggle
    const darkToggle = document.getElementById('darkToggle');
    darkToggle.onclick = () => {
      document.documentElement.toggleAttribute('data-theme','dark');
      darkToggle.innerHTML = document.documentElement.hasAttribute('data-theme')
        ? '<i class="fas fa-sun"></i>'
        : '<i class="fas fa-moon"></i>';
    };

    // Year in footer
    document.getElementById('year').textContent = new Date().getFullYear();

    // Password visibility toggle
    document.querySelector('.toggle-password').onclick = function(){
      const input = document.getElementById('txtpassword');
      input.type = input.type === 'password' ? 'text' : 'password';
      this.innerHTML = input.type === 'password'
        ? '<i class="fas fa-eye"></i>'
        : '<i class="fas fa-eye-slash"></i>';
    };
  </script>
</body>
</html>
