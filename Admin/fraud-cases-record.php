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

// Total fraud case count
$count_sql = "SELECT COUNT(*) as total FROM fraud_cases";
$count_result = $conn->query($count_sql);
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

// Fetch fraud case records
$sql = "SELECT 
            f.id AS case_id,
            f.scope,
            f.risk_score,
            f.status,
            f.opened_at,
            u.full_name
        FROM fraud_cases f
        INNER JOIN users u ON f.user_id = u.id
        ORDER BY f.opened_at DESC
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$cases = [];
while ($row = $result->fetch_assoc()) {
    $cases[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fraud Cases | <?php echo $app_name; ?></title>
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
                <h2 class="text-3xl font-bold text-indigo-700 mb-6">Fraud Cases</h2>

                <!-- Search Bar -->
                <div class="mb-6 flex flex-col md:flex-row gap-3 md:gap-4 items-center">
                    <input id="caseSearch" type="text" placeholder="Search by any column..." class="w-full md:w-80 px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition" autocomplete="off" />
                </div>

                <!-- Cases Table -->
                <div class="overflow-x-auto rounded-lg shadow">
                    <table class="min-w-full bg-white" id="casesTable">
                        <thead class="bg-indigo-50">
                            <tr>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">S/N</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">User</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Scope</th>
                                <th class="py-3 px-4 text-center font-semibold text-gray-700">Risk Score</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Status</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Opened At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($cases) > 0): ?>
                                <?php foreach ($cases as $index => $case): ?>
                                    <tr class="border-b hover:bg-indigo-50 transition">
                                        <td class="py-3 px-4 font-mono text-indigo-700"><?php echo (($page - 1) * $limit) + $index + 1; ?></td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($case['full_name']); ?></td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($case['scope']); ?></td>
                                        <td class="py-3 px-4 text-center"><?php echo ($case['risk_score']); ?></td>
                                        <td class="py-3 px-4"><?php echo ucfirst(htmlspecialchars($case['status'])); ?></td>
                                        <td class="py-3 px-4"><?php echo date('d M Y, h:i A', strtotime($case['opened_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="py-6 px-4 text-center text-gray-400">No fraud cases found.</td>
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
    document.getElementById('caseSearch').addEventListener('keyup', function () {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#casesTable tbody tr');
        rows.forEach(function (row) {
            let text = row.textContent.toLowerCase();
            row.style.display = text.indexOf(filter) > -1 ? '' : 'none';
        });
    });
    </script>
</body>
</html>
