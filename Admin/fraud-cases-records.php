<?php
include('../inc/config.php');
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}
$success = '';
$error = '';

// Pagination variables
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Count total fraud cases for pagination
$count_sql = "SELECT COUNT(fc.id) as total
    FROM fraud_cases fc
    LEFT JOIN users u ON fc.user_id = u.id";
$count_result = $conn->query($count_sql);
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

// Fetch fraud cases joined with users
$sql = "SELECT fc.id as case_id, fc.scope, fc.user_id, fc.risk_score, fc.status, fc.analyst_notes, fc.opened_at, fc.closed_at,
        u.full_name, u.email, u.phone
        FROM fraud_cases fc
        LEFT JOIN users u ON fc.user_id = u.id
        ORDER BY fc.id DESC
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
$cases = [];
while ($row = $result->fetch_assoc()) {
    $cases[] = $row;
}

// Handle status update (e.g., mark as resolved)
if (isset($_GET['resolve']) && is_numeric($_GET['resolve'])) {
    $case_id = intval($_GET['resolve']);
    $update_stmt = $conn->prepare("UPDATE fraud_cases SET status = 'cleared', closed_at = NOW() WHERE id = ?");
    $update_stmt->bind_param('i', $case_id);
    if ($update_stmt->execute()) {
        $success = "Fraud case marked as cleared.";
        header("Location: fraud-cases-records.php?success=1&page=" . $page);
        exit;
    } else {
        $error = "Failed to update fraud case.";
    }
}

// Show success message if redirected
if (isset($_GET['success'])) {
    $success = "Fraud case marked as cleared.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fraud Cases Records | <?php echo $app_name; ?></title>
    <?php include('partials/head.php'); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<script type="text/javascript">
function resolveCase(){
    return confirm("Are you sure you want to mark this case as cleared?");
}
</script>
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
                <h2 class="text-3xl font-bold text-indigo-700 mb-6">Fraud Cases Records</h2>
                <!-- Success/Error Messages -->
                <?php if ($success): ?>
                    <div class="mb-4 p-3 rounded bg-green-100 text-green-700"><?php echo $success; ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="mb-4 p-3 rounded bg-red-100 text-red-700"><?php echo $error; ?></div>
                <?php endif; ?>
                <!-- Search Bar -->
                <div class="mb-6 flex flex-col md:flex-row gap-3 md:gap-4 items-center">
                    <input id="caseSearch" type="text" placeholder="Search by any column..." class="w-full md:w-80 px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition" autocomplete="off" />
                </div>
                <!-- Fraud Cases Table -->
                <div class="overflow-x-auto rounded-lg shadow">
                    <table class="min-w-full bg-white" id="casesTable">
                        <thead class="bg-indigo-50">
                            <tr>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">S/N</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Name</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Email</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Scope</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Risk Score</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Status</th>
                                <th class="py-3 px-4 text-center font-semibold text-gray-700">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($cases) > 0): ?>
                                <?php foreach ($cases as $index => $case): ?>
                                    <tr class="border-b hover:bg-indigo-50 transition">
                                        <td class="py-3 px-4 font-mono text-indigo-700"><?php echo (($page - 1) * $limit) + $index + 1; ?></td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($case['full_name']); ?></td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($case['email']); ?></td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($case['scope']); ?></td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($case['risk_score']); ?></td>
                                        <td class="py-3 px-4">
                                            <?php
                                            $status_labels = [
                                                'open' => ['Open', 'bg-red-100 text-red-700'],
                                                'investigating' => ['Investigating', 'bg-yellow-100 text-yellow-700'],
                                                'confirmed_fraud' => ['Confirmed Fraud', 'bg-orange-100 text-orange-700'],
                                                'cleared' => ['Cleared', 'bg-green-100 text-green-700']
                                            ];
                                            $label = $status_labels[$case['status']] ?? ['Unknown', 'bg-gray-100 text-gray-700'];
                                            ?>
                                            <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full <?php echo $label[1]; ?>"><?php echo $label[0]; ?></span>
                                        </td>

                                        <td class="py-3 px-4 text-center">
                                            <?php if ($case['status'] != 'cleared'): ?>
                                                <a href="fraud-cases-records.php?resolve=<?php echo $case['case_id'];?>&page=<?php echo $page;?>" onClick="return resolveCase();">
                                                    <i class="fa fa-check" aria-hidden="true" title="Mark as Cleared"></i>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="11" class="py-6 px-4 text-center text-gray-400">No fraud cases found.</td>
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
    // Simple search for the fraud cases table
    document.getElementById('caseSearch').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#casesTable tbody tr');
        rows.forEach(function(row) {
            let text = row.textContent.toLowerCase();
            row.style.display = text.indexOf(filter) > -1 ? '' : 'none';
        });
    });
    </script>
</body>
</html>
