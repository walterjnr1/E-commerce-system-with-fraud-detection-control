<?php
include('config.php');

// Initialize variables
$successMsg = $errorMsg = '';
$showPassword = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnadd'])) {
  // Get and sanitize input
  $full_name = trim($_POST['signupName'] ?? '');
  $email = trim($_POST['signupEmail'] ?? '');
  $password = $_POST['signupPassword'] ?? '';
  $phone = trim($_POST['signupPhone'] ?? '');

  // Validate input
  if (!$full_name) {
    $errorMsg = "Full name is required.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errorMsg = "Valid email is required.";
  } elseif (strlen($password) < 6) {
    $errorMsg = "Password must be at least 6 characters.";
  } else {
    // Check if user exists
    $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
      $errorMsg = "An account with this email already exists.";
    } else {
      // Insert new user
      $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

      $role = 'customer';
      $stmt_insert = $conn->prepare("INSERT INTO users (email, password_hash, full_name, phone, role) VALUES (?, ?, ?, ?, ?)");
      $stmt_insert->bind_param("sssss", $email, $password_hash, $full_name, $phone, $role);
      if ($stmt_insert->execute()) {
        $successMsg = "Account created successfully!<br>Email: " . htmlspecialchars($email) . "<br>Password: " . htmlspecialchars($password) . "<br>You can now <a href='login.php'>login here</a>.";
        $user_id = $stmt_insert->insert_id;
        // Activity log
        $operation = "created an account as $role on: " . date('Y-m-d H:i:s');
        log_activity($conn, $user_id, $role, $operation, $ip_address);
      } else {
        $errorMsg = "Error creating account. Please try again.";
      }
      $stmt_insert->close();
    }
    $stmt->close();
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sign up - TechParts</title>
  <?php include('partials/head.php'); ?>
  <link rel="stylesheet" href="assets/css/signup_style.css">
</head>
<body>
  <!-- Header -->
     <header class="header" id="header">
  <?php include('partials/navbar.php'); ?>
  </header>

  <!-- Signup section -->
  <main class="signup-section">
  <div class="signup-card" role="form" aria-labelledby="signupTitle">
    <h1 id="signupTitle">Create Your Account</h1>

     <?php if ($errorMsg): ?>
    <div class="alert alert-danger"><?php echo $errorMsg; ?></div>
    <?php endif; ?>
    <?php if ($successMsg): ?>
    <div class="alert alert-success"><?php echo $successMsg; ?></div>
    <?php endif; ?>
    <form id="signupForm" method="post" autocomplete="off" novalidate>
    <div class="form-group">
      <label for="signupName">Full Name</label>
      <input type="text" id="signupName" name="signupName" autocomplete="name" required placeholder="Your full name" aria-required="true" value="<?= htmlspecialchars($_POST['signupName'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label for="signupEmail">Email</label>
      <input type="email" id="signupEmail" name="signupEmail" autocomplete="email" required placeholder="you@email.com" aria-required="true" value="<?= htmlspecialchars($_POST['signupEmail'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label for="signupPhone">Phone (optional)</label>
      <input type="text" id="signupPhone" name="signupPhone" autocomplete="tel" placeholder="Phone number" value="<?= htmlspecialchars($_POST['signupPhone'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label for="signupPassword">Password</label>
      <div class="password-field">
      <input type="password" id="signupPassword" name="signupPassword" autocomplete="new-password" required placeholder="Create a password" aria-required="true">
      <button type="button" class="toggle-password" aria-label="Show password"><i class="fas fa-eye"></i></button>
      </div>
    </div>
    <button type="submit" name="btnadd" class="submit-btn">Create Account</button>
    <div class="auth-extra">Already have an account? <a href="login.php">Login</a></div>
    </form>
  </div>
  </main>

  <!-- Footer -->
  <footer class="footer">
<?php  include('partials/footer.php') ?> 
  </footer>

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
    const input = document.getElementById('signupPassword');
    input.type = input.type === 'password' ? 'text' : 'password';
    this.innerHTML = input.type === 'password'
    ? '<i class="fas fa-eye"></i>'
    : '<i class="fas fa-eye-slash"></i>';
  };
  </script>
</body>
</html>
