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

// Total order count
$count_sql = "SELECT COUNT(*) as total FROM orders";
$count_result = $conn->query($count_sql);
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

// Fetch order records with product list
$sql = "SELECT 
            o.id AS order_id,
            o.total,
            o.status AS order_status,
            u.full_name,
            c.status AS cart_status,
            GROUP_CONCAT(CONCAT(p.name, ' (x', ci.qty, ')') SEPARATOR ', ') AS products_ordered
        FROM orders o
        INNER JOIN users u ON o.user_id = u.id
        INNER JOIN carts c ON o.cart_id = c.id
        INNER JOIN cart_items ci ON ci.cart_id = c.id
        INNER JOIN products p ON ci.product_id = p.id
        GROUP BY o.id
        ORDER BY o.id DESC
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Records | <?php echo $app_name; ?></title>
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
                <h2 class="text-3xl font-bold text-indigo-700 mb-6">Order Records</h2>

                <!-- Search Bar -->
                <div class="mb-6 flex flex-col md:flex-row gap-3 md:gap-4 items-center">
                    <input id="orderSearch" type="text" placeholder="Search by any column..." class="w-full md:w-80 px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition" autocomplete="off" />
                </div>

                <!-- Orders Table -->
                <div class="overflow-x-auto rounded-lg shadow">
                    <table class="min-w-full bg-white" id="ordersTable">
                        <thead class="bg-indigo-50">
                            <tr>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">S/N</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Customer</th>
                                <th class="py-3 px-4 text-right font-semibold text-gray-700">Total</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Order Status</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Cart Status</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Products Ordered</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($orders) > 0): ?>
                                <?php foreach ($orders as $index => $order): ?>
                                    <tr class="border-b hover:bg-indigo-50 transition">
                                        <td class="py-3 px-4 font-mono text-indigo-700"><?php echo (($page - 1) * $limit) + $index + 1; ?></td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($order['full_name']); ?></td>
                                        <td class="py-3 px-4 text-right"><?php echo $currency . number_format($order['total'], 2); ?></td>
                                        <td class="py-3 px-4"><?php echo ucfirst(htmlspecialchars($order['order_status'])); ?></td>
                                        <td class="py-3 px-4"><?php echo ucfirst(htmlspecialchars($order['cart_status'])); ?></td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($order['products_ordered']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="py-6 px-4 text-center text-gray-400">No order records found.</td>
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

    <!-- Search Script -->
    <script>
    document.getElementById('orderSearch').addEventListener('keyup', function () {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#ordersTable tbody tr');
        rows.forEach(function (row) {
            let text = row.textContent.toLowerCase();
            row.style.display = text.indexOf(filter) > -1 ? '' : 'none';
        });
    });
    </script>
</body>
</html>
