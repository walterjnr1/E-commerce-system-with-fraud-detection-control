<?php
include('../inc/config.php');
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Pagination setup
$limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// Count total payments for this manager's events
$count_stmt = $conn->prepare(" SELECT COUNT(*) as total
    FROM payments p INNER JOIN events e ON p.event_id = e.id
    WHERE e.manager_id = ?
");
$count_stmt->bind_param("i", $manager_id);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_payments = $count_result->fetch_assoc()['total'] ?? 0;
$count_stmt->close();

$total_pages = max(1, ceil($total_payments / $limit));

// Fetch payment records for this manager's events
$stmt = $conn->prepare(" SELECT p.id as payment_id,p.amount,p.payment_method, p.payment_status, p.payment_reference, p.description, p.paid_at, u.name as user_name, u.email as user_email,
 u.phone as user_phone, e.name as event_name, e.event_datetime, e.venue as event_venue FROM payments p  INNER JOIN users u ON p.user_id = u.id INNER JOIN events e ON p.event_id = e.id WHERE e.manager_id = ?
 ORDER BY p.paid_at DESC LIMIT ? OFFSET ? ");
$stmt->bind_param("iii", $manager_id, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$payments = [];
while ($row = $result->fetch_assoc()) {
    $payments[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Records | <?php echo $app_name; ?></title>
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
                <h2 class="text-3xl font-bold text-indigo-700 mb-6">Payment Records</h2>
                <!-- Success/Error Messages -->
                <?php if (!empty($success)): ?>
                    <div class="mb-4 p-3 rounded bg-green-100 text-green-700"><?php echo $success; ?></div>
                <?php endif; ?>
                <?php if (!empty($error)): ?>
                    <div class="mb-4 p-3 rounded bg-red-100 text-red-700"><?php echo $error; ?></div>
                <?php endif; ?>
                <!-- Search Bar -->
                <div class="mb-6 flex flex-col md:flex-row gap-3 md:gap-4 items-center">
                    <input id="paymentSearch" type="text" placeholder="Search by any column..." class="w-full md:w-80 px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition" autocomplete="off" />
                </div>
                <!-- Payments Table -->
                <div class="overflow-x-auto rounded-lg shadow">
                    <table class="min-w-full bg-white" id="paymentsTable">
                        <thead class="bg-indigo-50">
                            <tr>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">S/N</th>
                                  <th class="py-3 px-4 text-left font-semibold text-gray-700">Event</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Event Date</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Venue</th>
                                <th class="py-3 px-4 text-right font-semibold text-gray-700">Amount</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Method</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Status</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Reference</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Description</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Paid At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($payments) > 0): ?>
                                <?php foreach ($payments as $index => $payment): ?>
                                    <tr class="border-b hover:bg-indigo-50 transition">
                                        <td class="py-3 px-4 font-mono text-indigo-700"><?php echo (($page - 1) * $limit) + $index + 1; ?></td>

                                        <td class="py-3 px-4"><?php echo htmlspecialchars($payment['event_name']); ?></td>
                                        <td class="py-3 px-4"><?php echo date('M d, Y H:i', strtotime($payment['event_datetime'])); ?></td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($payment['event_venue']); ?></td>
                                        <td class="py-3 px-4 text-right"><?php echo $currency; ?> <?php echo number_format($payment['amount'], 2); ?></td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                                        <td class="py-3 px-4">
                                            <?php
                                            $status = strtolower($payment['payment_status']);
                                            if ($status === 'success' || $status === 'completed') {
                                                echo '<span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">'.ucfirst($status).'</span>';
                                            } elseif ($status === 'pending') {
                                                echo '<span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">Pending</span>';
                                            } elseif ($status === 'failed' || $status === 'cancelled') {
                                                echo '<span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">'.ucfirst($status).'</span>';
                                            } else {
                                                echo '<span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">'.htmlspecialchars($payment['payment_status']).'</span>';
                                            }
                                            ?>
                                        </td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($payment['payment_reference']); ?></td>
                                        <td class="py-3 px-4 max-w-xs truncate" title="<?php echo htmlspecialchars($payment['description']); ?>">
                                            <?php echo htmlspecialchars(mb_strimwidth($payment['description'], 0, 60, '...')); ?>
                                        </td>
                                        <td class="py-3 px-4"><?php echo date('M d, Y H:i', strtotime($payment['paid_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="13" class="py-6 px-4 text-center text-gray-400">No payment records found.</td>
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
    // Simple search filter for payments table
    document.getElementById('paymentSearch').addEventListener('input', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#paymentsTable tbody tr');
        rows.forEach(row => {
            let text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });
    </script>
</body>
</html>
