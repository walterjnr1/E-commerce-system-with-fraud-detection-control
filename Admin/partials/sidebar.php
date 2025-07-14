<?php $initial = !empty($row_user['full_name']) ? strtoupper(mb_substr(trim($row_user['full_name']), 0, 1)) : 'N/A'; ?>
<div class="mb-6 flex flex-col items-center">
  <div class="w-20 h-20 rounded-full bg-indigo-100 flex items-center justify-center text-3xl font-bold text-indigo-700 mb-2">
    <?php echo $initial; ?>
  </div>
  <span class="font-semibold text-lg"><?php echo $row_user['full_name'];?></span>
  <span class="text-gray-400 text-sm"><?php echo $row_user['email'];?></span>
</div>
<nav class="flex flex-col gap-1">
  <!-- Dashboard -->
  <a href="index.php" class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-indigo-50 transition font-medium text-gray-700">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h3a1 1 0 001-1v-4h4v4a1 1 0 001 1h3a1 1 0 001-1V10" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    Dashboard
  </a>
  <!-- New Products -->
  <a href="add-products.php" class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-indigo-50 transition font-medium text-gray-700">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path d="M12 4v16m8-8H4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      <rect x="4" y="4" width="16" height="16" rx="2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    New Products
  </a>
  
  
  <!-- Login Attempts -->
  <a href="login-attempts.php" class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-indigo-50 transition font-medium">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    Login Attempts
  </a>
   <!-- checkout Attempts -->
  <a href="checkout-attempts.php" class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-indigo-50 transition font-medium">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    Checkout Attempts
  </a>
  <!-- Profile -->
  <a href="profile.php" class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-indigo-50 transition font-medium">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path d="M5.121 17.804A13.937 13.937 0 0112 15c2.485 0 4.797.755 6.879 2.047M15 11a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    Profile
  </a>
  <!-- Fraud Cases -->
  <a href="fraud-cases-records.php" class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-indigo-50 transition font-medium">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path d="M9 17v-2a4 4 0 018 0v2M12 7a4 4 0 110 8 4 4 0 010-8z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    Fraud Cases
  </a>
  <!-- users -->
  <a href="user-record.php" class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-indigo-50 transition font-medium">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <rect x="3" y="7" width="18" height="10" rx="2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      <path d="M9 9l6 6m0-6l-6 6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  User Record
  </a>
  <!-- Black-Listed IP Address -->
  <a href="black-listed-users.php" class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-indigo-50 transition font-medium">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <rect x="3" y="7" width="18" height="10" rx="2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      <path d="M9 9l6 6m0-6l-6 6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    Black-Listed Customer
  </a>
  <!-- Activity Logs -->
  <a href="activity-logs.php" class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-indigo-50 transition font-medium">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <circle cx="12" cy="12" r="10" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      <path d="M12 6v6l4 2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    Activity Logs
  </a>
  <!-- Logout -->
  <a href="logout.php" class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-red-50 transition font-medium text-red-600 mt-4">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    Logout
  </a>
</nav>