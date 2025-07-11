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
$count_sql = "SELECT COUNT(la.id) as total
    FROM login_attempts la
    LEFT JOIN users u ON la.user_id = u.id
    LEFT JOIN addresses a ON u.id = a.user_id";
$count_result = $conn->query($count_sql);
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

// Fetch login attempts with user and address info
$sql = "SELECT 
        la.id as attempt_id,
        la.email_submitted,
        la.user_agent,
        la.success,
        la.failure_reason,
        la.created_at,
        u.id as user_id,
        u.full_name,
        u.email as user_email,
        u.role,
        u.status,
        a.city,
        a.state,
        a.country
    FROM login_attempts la
    LEFT JOIN users u ON la.user_id = u.id
    LEFT JOIN addresses a ON u.id = a.user_id AND a.label = 'shipping'
    ORDER BY la.created_at DESC
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
    <title>Login Attempts | <?php echo $app_name; ?></title>
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
                <h2 class="text-3xl font-bold text-indigo-700 mb-6">Login Attempts</h2>
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
                <!-- Login Attempts Table -->
                <div class="overflow-x-auto rounded-lg shadow">
                    <table class="min-w-full bg-white" id="attemptsTable">
                        <thead class="bg-indigo-50">
                            <tr>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">S/N</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Date/Time</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Email Submitted</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">User</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">User Agent</th>
                                <th class="py-3 px-4 text-center font-semibold text-gray-700">Result</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Failure Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($attempts) > 0): ?>
                                <?php foreach ($attempts as $index => $attempt): ?>
                                    <tr class="border-b hover:bg-indigo-50 transition">
                                        <td class="py-3 px-4 font-mono text-indigo-700"><?php echo (($page - 1) * $limit) + $index + 1; ?></td>
                                        <td class="py-3 px-4"><?php echo date('M d, Y H:i', strtotime($attempt['created_at'])); ?></td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($attempt['email_submitted']); ?></td>
                                        <td class="py-3 px-4">
                                            <?php
                                            if ($attempt['user_id']) {
                                                echo htmlspecialchars($attempt['full_name']) . "<br><span class='text-xs text-gray-500'>" . htmlspecialchars($attempt['user_email']) . "</span>";
                                            } else {
                                                echo '<span class="text-gray-400 italic">Unknown</span>';
                                            }
                                            ?>
                                        </td>
                                        
                                        
                                        <td class="py-3 px-4 text-xs break-all"><?php echo htmlspecialchars($attempt['user_agent']); ?></td>
                                        <td class="py-3 px-4 text-center">
                                            <?php
                                            if ($attempt['success']) {
                                                echo '<span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Success</span>';
                                            } else {
                                                echo '<span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">Failed</span>';
                                            }
                                            ?>
                                        </td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($attempt['failure_reason']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="py-6 px-4 text-center text-gray-400">No login attempts found.</td>
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
    // Simple search for the login attempts table
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
