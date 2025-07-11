<?php
include('../inc/config.php');
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}
$success = '';
$error = '';

// Pagination setup
$limit = 20;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Count total records for pagination
$count_sql = "SELECT COUNT(wr.id) as total
    FROM withdrawal_request wr
    WHERE wr.manager_id = ?";
$stmt = $conn->prepare($count_sql);
$stmt->bind_param('i', $manager_id);
$stmt->execute();
$count_result = $stmt->get_result();
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

// Fetch withdrawal request records
$sql = "SELECT 
        wr.id as request_id,
        wr.amount,
        wr.status,
        wr.requested_at,
        u.name as manager_name
    FROM withdrawal_request wr
    INNER JOIN users u ON wr.manager_id = u.id
    WHERE wr.manager_id = ?
    ORDER BY wr.requested_at DESC
    LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('iii', $manager_id, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
$withdrawals = [];
while ($row = $result->fetch_assoc()) {
    $withdrawals[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Withdrawal Requests | <?php echo $app_name; ?></title>
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
                <h2 class="text-3xl font-bold text-indigo-700 mb-6">Withdrawal Requests</h2>
                <!-- Success/Error Messages -->
                <?php if ($success): ?>
                    <div class="mb-4 p-3 rounded bg-green-100 text-green-700"><?php echo $success; ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="mb-4 p-3 rounded bg-red-100 text-red-700"><?php echo $error; ?></div>
                <?php endif; ?>
                <!-- Search Bar -->
                <div class="mb-6 flex flex-col md:flex-row gap-3 md:gap-4 items-center">
                    <input id="withdrawalSearch" type="text" placeholder="Search by any column..." class="w-full md:w-80 px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition" autocomplete="off" />
                </div>
                <!-- Withdrawal Requests Table -->
                <div class="overflow-x-auto rounded-lg shadow">
                    <table class="min-w-full bg-white" id="withdrawalsTable">
                        <thead class="bg-indigo-50">
                            <tr>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">S/N</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Manager Name</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Amount</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Status</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Requested At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($withdrawals) > 0): ?>
                                <?php foreach ($withdrawals as $index => $withdrawal): ?>
                                    <tr class="border-b hover:bg-indigo-50 transition">
                                        <td class="py-3 px-4 font-mono text-indigo-700"><?php echo (($page - 1) * $limit) + $index + 1; ?></td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($withdrawal['manager_name']); ?></td>
                                        <td class="py-3 px-4 font-mono"><?php echo $currency; ?><?php echo number_format($withdrawal['amount'], 2); ?></td>
                                        <td class="py-3 px-4">
                                            <?php
                                            $status = strtolower($withdrawal['status']);
                                            if ($status === 'pending') {
                                                // Orange warning color
                                                echo '<span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-700">Pending</span>';
                                            } elseif ($status === 'approved') {
                                                // Green color
                                                echo '<span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Approved</span>';
                                            } elseif ($status === 'rejected') {
                                                // Red color
                                                echo '<span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">Rejected</span>';
                                            } else {
                                                // Default: blue for unknown/other statuses
                                                echo '<span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">'.htmlspecialchars($withdrawal['status']).'</span>';
                                            }
                                            ?>
                                        </td>
                                        <td class="py-3 px-4"><?php echo date('M d, Y H:i', strtotime($withdrawal['requested_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="py-6 px-4 text-center text-gray-400">No withdrawal requests found.</td>
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
    // Simple search for the withdrawals table
    document.getElementById('withdrawalSearch').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#withdrawalsTable tbody tr');
        rows.forEach(function(row) {
            let text = row.textContent.toLowerCase();
            row.style.display = text.indexOf(filter) > -1 ? '' : 'none';
        });
    });
    </script>
</body>
</html>
