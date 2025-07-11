<div class="max-w-6xl mx-auto flex items-center justify-between px-6 py-4">
    <a href="index" class="navbar-logo flex items-center gap-2">
        <?php if (!empty($app_logo)): ?>
            <img src="../<?php echo htmlspecialchars($app_logo); ?>" alt="Logo" width="66" height="66" >
        <?php else: ?>
            <svg class="w-8 h-8 text-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M8 12l2 2 4-4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        <?php endif; ?>
        <?php echo htmlspecialchars($app_name ?? 'QuickPass'); ?>
    </a>
    <div class="desktop-nav flex items-center space-x-6">
    <a href="index.php" class="hover:text-indigo-600 font-semibold">Home</a>
    <a href="ticket.php" class="hover:text-indigo-600 font-semibold">Ticket</a>
    <a href="profile.php" class="hover:text-indigo-600 font-semibold">Profile</a>
    <a href="logout.php" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-4 py-2 rounded font-semibold shadow hover:from-indigo-700 hover:to-purple-700 transition text-sm">Logout</a>
  </div>
  <!-- Mobile Nav Toggle -->
  <button id="mobileNavToggle" class="mobile-nav-toggle" aria-label="Open Menu">&#9776;</button>
  <!-- Mobile Nav Menu -->
  <div id="mobileNavMenu" class="mobile-nav">
    <a href="index.php" class="mobile-nav-link">Home</a>
    <a href="ticket.php" class="mobile-nav-link">Ticket</a>
    <a href="Profile.php" class="mobile-nav-link">Profile</a>
    <a href="logout.php" class="mobile-nav-link">Logout</a>
  </div>
</div>