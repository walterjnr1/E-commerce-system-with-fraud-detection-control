<?php
include('../inc/config.php');
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}
$success = '';
$error = '';

// Pagination setup
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Count total records for pagination
$count_sql = "SELECT COUNT(rs.id) as total
    FROM risk_signals rs
    LEFT JOIN orders o ON rs.order_id = o.id
    LEFT JOIN users u ON rs.user_id = u.id";
$count_result = $conn->query($count_sql);
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

// Fetch checkout attempts with risk signals, order, and user info
$sql = "SELECT 
        rs.id as signal_id,
        rs.created_at as signal_created,
        rs.signal_type,
        rs.signal_value,
        rs.weight,
        o.id as order_id,
        o.cart_id,
        o.address_id,
        o.total as order_total,
        o.status as order_status,
        o.created_at as order_created,
        u.id as user_id,
        u.full_name,
        u.email as user_email,
        u.role,
        u.status as user_status
    FROM risk_signals rs
    LEFT JOIN orders o ON rs.order_id = o.id
    LEFT JOIN users u ON rs.user_id = u.id
    ORDER BY rs.created_at DESC
    LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
$attempts = [];
while ($row = $result->fetch_assoc()) {
    $attempts[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout Attempts | <?php echo $app_name; ?></title>
    <?php include('partials/head.php'); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800">
    <!-- Navbar -->
    <header class="fixed top-0 left-0 w-full bg-white shadow z-50">
        <?php include('partials/navbar.php'); ?>
    </header>

    <section class="min-h-screen flex items-start justify-center pt-24 pb-12">
        <div class="w-full max-w-6xl mx-auto flex flex-col md:flex-row gap-8">
            <!-- Sidebar -->
            <aside class="w-full md:w-72 bg-white rounded-2xl shadow-2xl p-6 flex flex-col gap-2 sticky top-28 z-10 md:order-1 order-2 mb-8 md:mb-0">
                <?php include('partials/sidebar.php'); ?>
            </aside>
            <!-- Main Content -->
            <div class="flex-1 bg-white rounded-2xl shadow-2xl p-8 mb-8 md:mb-0 md:order-2 order-1">
                <h2 class="text-3xl font-bold text-indigo-700 mb-6">Checkout Attempts</h2>
                <!-- Success/Error Messages -->
                <?php if ($success): ?>
                    <div class="mb-4 p-3 rounded bg-green-100 text-green-700"><?php echo $success; ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="mb-4 p-3 rounded bg-red-100 text-red-700"><?php echo $error; ?></div>
                <?php endif; ?>
                <!-- Search Bar -->
                <div class="mb-6 flex flex-col md:flex-row gap-3 md:gap-4 items-center">
                    <input id="attemptSearch" type="text" placeholder="Search by any column..." class="w-full md:w-80 px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition" autocomplete="off" />
                </div>
                <!-- Checkout Attempts Table -->
                <div class="overflow-x-auto rounded-lg shadow">
                    <table class="min-w-full bg-white" id="attemptsTable">
                        <thead class="bg-indigo-50">
                            <tr>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">S/N</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Date/Time</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Order ID</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">User</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Order Total</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Order Status</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Signal Type</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Weight</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($attempts) > 0): ?>
                                <?php foreach ($attempts as $index => $attempt): ?>
                                    <tr class="border-b hover:bg-indigo-50 transition">
                                        <td class="py-3 px-4 font-mono text-indigo-700"><?php echo (($page - 1) * $limit) + $index + 1; ?></td>
                                        <td class="py-3 px-4"><?php echo date('M d, Y H:i', strtotime($attempt['signal_created'])); ?></td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($attempt['order_id']); ?></td>
                                        <td class="py-3 px-4">
                                            <?php
                                            if ($attempt['user_id']) {
                                                echo htmlspecialchars($attempt['full_name']) . "<br><span class='text-xs text-gray-500'>" . htmlspecialchars($attempt['user_email']) . "</span>";
                                            } else {
                                                echo '<span class="text-gray-400 italic">Unknown</span>';
                                            }
                                            ?>
                                        </td>
                                        <td class="py-3 px-4">N<?php echo htmlspecialchars(number_format($attempt['order_total'], 2)); ?></td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($attempt['order_status']); ?></td>
                                      
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($attempt['signal_type']); ?></td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($attempt['weight']); ?></td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($attempt['notes']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="11" class="py-6 px-4 text-center text-gray-400">No checkout attempts found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <?php include('partials/pagination.php'); ?>
            </div>
        </div>
    </section>
    <!-- Footer -->
    <footer class="py-6 text-center text-gray-600 bg-gray-100">
        <?php include('../partials/footer.php'); ?>
    </footer>
    <script>
    // Simple search for the checkout attempts table
    document.getElementById('attemptSearch').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#attemptsTable tbody tr');
        rows.forEach(function(row) {
            let text = row.textContent.toLowerCase();
            row.style.display = text.indexOf(filter) > -1 ? '' : 'none';
        });
    });
    </script>
</body>
</html>
