  <nav class="nav container" aria-label="Main navigation">
      <a href="#" class="logo" aria-label="TechParts Home">
        <i class="fas fa-microchip"></i> TechParts
      </a>
      <button class="nav-toggle" id="navToggle" aria-label="Open menu" aria-expanded="false">
        <span class="nav-toggle-bar"></span>
        <span class="nav-toggle-bar"></span>
        <span class="nav-toggle-bar"></span>
      </button>
      <ul class="nav-menu" id="navMenu">
        <?php if (empty($_SESSION['user_id'])): ?>
          <li><a href="login.php" class="nav-link">login</a></li>
          <li><a href="signup.php" class="nav-link">Sign up</a></li>
          <li><a href="#products" class="nav-link">Products</a></li>
          <li><button class="nav-link" id="searchBtn" aria-label="Search products"><i class="fas fa-search"></i></button></li>
        <?php else: ?>
          <li style="font-weight:600;color:var(--primary);padding-right:.5em;">
          Welcome <?php echo htmlspecialchars($_SESSION['fullname'] ?? ''); ?>
          </li>
          <li><a href="#products" class="nav-link">Products</a></li>
          <li><button class="nav-link" id="searchBtn" aria-label="Search products"><i class="fas fa-search"></i></button></li>
          <li>
          <button class="cart-btn" id="cartBtn" aria-label="View cart">
            <i class="fas fa-shopping-cart"></i>
            <span class="cart-count" id="cartCount" aria-live="polite">0</span>
          </button>
          </li>
          <li><a href="profile.php" class="nav-link">Profile</a></li>
          <li><a href="logout.php" class="nav-link">Logout</a></li>

        <?php endif; ?>
      </ul>
    </nav>
