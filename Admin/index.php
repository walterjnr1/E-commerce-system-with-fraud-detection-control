<?php 
include('../inc/config.php');
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Dashboard | <?php echo $app_name; ?></title>
    <?php include('partials/head.php'); ?>
</head>
<body class="bg-gray-50 text-gray-800">
    <!-- Navbar -->
    <header class="fixed top-0 left-0 w-full bg-white shadow z-50">
         <?php include('partials/navbar.php'); ?>
    </header>

    <section class="min-h-screen flex items-start justify-center pt-24 pb-12">
      <div class="w-full max-w-6xl mx-auto flex flex-col md:flex-row gap-8">
            <!-- Left Sidebar Menu -->
            <aside class="w-full md:w-72 bg-white rounded-2xl shadow-2xl p-6 flex flex-col gap-2 sticky top-28 z-10 md:order-1 order-2 mb-8 md:mb-0">
             
                  <?php include('partials/sidebar.php'); ?>
            </aside>
            <!-- Main Content -->
            <div class="flex-1 bg-white rounded-2xl shadow-2xl p-8 mb-8 md:mb-0 md:order-2 order-1">
                <h2 class="text-3xl font-bold text-indigo-700 mb-6">Welcome, <?php echo htmlspecialchars($row_user['full_name'] ?? 'User'); ?>!</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Example Dashboard Cards -->
                    
                    <div class="bg-indigo-50 rounded-xl p-6 shadow flex flex-col">
                        <span class="text-indigo-600 font-semibold text-lg mb-2">Products</span>
                        <span class="text-3xl font-bold mb-1"><?php //echo $no_manager_tickets['total'] ?></span>
                        <a href="ticket-record" class="text-indigo-700 hover:underline mt-auto">View Tickets</a>                    
                    </div>
                    
                    <div class="bg-green-50 rounded-xl p-6 shadow flex flex-col">
                        <span class="text-green-600 font-semibold text-lg mb-2">Transactions</span>
                        <span class="text-3xl font-bold mb-1"><?php //echo $no_manager_transactions['total'] ?></span>
                        <a href="payments" class="text-green-700 hover:underline mt-auto">View Transactions</a>                    
                    </div>
                    <div class="bg-yellow-50 rounded-xl p-6 shadow flex flex-col">
                        <span class="text-yellow-600 font-semibold text-lg mb-2">Fraud Cases</span>
                        <span class="text-3xl font-bold mb-1"><?php //echo $no_manager_event['total'] ?></span>
                        <a href="event-record" class="text-yellow-700 hover:underline mt-auto">View Event</a>                    
                    </div>
                    <div class="bg-red-50 rounded-xl p-6 shadow flex flex-col">
                        <span class="text-red-600 font-semibold text-lg mb-2">Orders</span>
                        <span class="text-3xl font-bold mb-1"><?php //echo $no_manager_event['total'] ?></span>
                        <a href="event-record" class="text-red-700 hover:underline mt-auto">View Event</a>                    
                    </div>
                </div>
                
                <?php
                // Fetch recent activity for the logged-in user (limit 8)
                $stmt = $conn->prepare("SELECT operation, created_at, ip_address FROM activity_logs ORDER BY created_at DESC LIMIT 8");
                $stmt->execute();
                $result = $stmt->get_result();
                ?>
                <div class="mt-10">
                    <h3 class="text-xl font-semibold text-gray-700 mb-4">Recent Activity  </h3>
                    <ul class="divide-y divide-gray-200">
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <li class="py-3 flex justify-between items-center">
                                    <div>
                                        <span><?php echo htmlspecialchars($row['operation']); ?></span>
                                        <span class="ml-2 text-xs text-gray-400">(IP: <?php echo htmlspecialchars($row['ip_address']); ?>)</span>                                    </div>
                                    <span class="text-gray-400 text-sm">
                                        <?php
                                        // Display relative time (e.g., "2 days ago")
                                        $created_at = new DateTime($row['created_at']);
                                        $now = new DateTime();
                                        $diff = $now->diff($created_at);

                                        if ($diff->y > 0) {
                                            echo $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
                                        } elseif ($diff->m > 0) {
                                            echo $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
                                        } elseif ($diff->d > 0) {
                                            echo $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
                                        } elseif ($diff->h > 0) {
                                            echo $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
                                        } elseif ($diff->i > 0) {
                                            echo $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
                                        } else {
                                            echo 'Just now';
                                        }
                                        ?>
                                    </span>                                </li>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <li class="py-3 text-gray-500">No recent activity found.</li>
                        <?php endif; ?>
                    </ul>
                </div>
                <?php
                $stmt->close();
                ?>
            </div>
        </div>
</section>

    <!-- Footer -->
    <footer class="py-6 text-center text-gray-600 bg-gray-100">
        <?php include('../partials/footer.php'); ?>
    </footer>
</body>
</html>
