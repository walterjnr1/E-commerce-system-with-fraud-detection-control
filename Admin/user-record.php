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

// Count total user records for pagination
$count_sql = "SELECT COUNT(u.id) as total
    FROM users u
    LEFT JOIN addresses a ON u.id = a.user_id";
$count_result = $conn->query($count_sql);
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

// Fetch user records with address
$sql = "SELECT u.id, u.email, u.full_name, u.phone, u.role, u.status,
        a.line1, a.city, a.state FROM users u LEFT JOIN addresses a ON u.id = a.user_id where u.role = 'customer' ORDER BY u.id DESC
    LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

// Handle blacklist action
if (isset($_GET['blacklist']) && is_numeric($_GET['blacklist'])) {
    $user_id = intval($_GET['blacklist']);
    // Set status to 0 (inactive/blacklisted)
    $update_stmt = $conn->prepare("UPDATE users SET status = 0 WHERE id = ?");
    $update_stmt->bind_param('i', $user_id);
    if ($update_stmt->execute()) {
        $success = "User has been blacklisted.";
        // Refresh to avoid resubmission
        header("Location: user-record.php?success=1&page=" . $page);
        exit;
    } else {
        $error = "Failed to blacklist user.";
    }
}

// Show success message if redirected
if (isset($_GET['success'])) {
    $success = "User has been blacklisted.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Records | <?php echo $app_name; ?></title>
    <?php include('partials/head.php'); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
 <script type="text/javascript">
		function enable(){
if(confirm("ARE YOU SURE YOU WISH TO ENABLE THIS ACCOUNT ?" ))
{
return  true;
}
else {return false;
}
	 
}

</script>
<script type="text/javascript">
function disable(){
if(confirm("ARE YOU SURE YOU WISH TO DISABLE THIS ACCOUNT ?" ))
{
return  true;
}
else {return false;
}
	 
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
                <h2 class="text-3xl font-bold text-indigo-700 mb-6">User Records</h2>
                <!-- Success/Error Messages -->
                <?php if ($success): ?>
                    <div class="mb-4 p-3 rounded bg-green-100 text-green-700"><?php echo $success; ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="mb-4 p-3 rounded bg-red-100 text-red-700"><?php echo $error; ?></div>
                <?php endif; ?>
                <!-- Search Bar -->
                <div class="mb-6 flex flex-col md:flex-row gap-3 md:gap-4 items-center">
                    <input id="userSearch" type="text" placeholder="Search by any column..." class="w-full md:w-80 px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition" autocomplete="off" />
                </div>
                <!-- Users Table -->
                <div class="overflow-x-auto rounded-lg shadow">
                    <table class="min-w-full bg-white" id="usersTable">
                        <thead class="bg-indigo-50">
                            <tr>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">S/N</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Full Name</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Email</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Phone</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Role</th>
                                <th class="py-3 px-4 text-center font-semibold text-gray-700">Status</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Address</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">City</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">State</th>
                                <th class="py-3 px-4 text-center font-semibold text-gray-700">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($users) > 0): ?>
                                <?php foreach ($users as $index => $user): ?>
                                    <tr class="border-b hover:bg-indigo-50 transition">
                                        <td class="py-3 px-4 font-mono text-indigo-700"><?php echo (($page - 1) * $limit) + $index + 1; ?></td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($user['full_name']); ?></td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($user['phone']); ?></td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($user['role']); ?></td>
                                        <td class="py-3 px-4 text-center">
                                            <?php
                                            if ($user['status'] == 1) {
                                                echo '<span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Active</span>';
                                            } else {
                                                echo '<span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">Blacklisted</span>';
                                            }
                                            ?>
                                        </td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($user['line1']); ?></td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($user['city']); ?></td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($user['state']); ?></td>
                                        <td class="py-3 px-4 text-center">
                                            <?php if (($user['status'])==(('0')))  { ?>
              <a href="blacklist-user.php?eid=<?php echo $user['id'];?>" onClick="return enable();">
        <i class="fa fa-check" aria-hidden="true" title="Remove user from Blacklist User"></i>
      </a>
              <?php } else {?>
                <a href="blacklist-user.php?did=<?php echo $user['id'];?>" onClick="return disable();">
      <i class="fa fa-times" aria-hidden="true" title="Disable User"></i>
      </a>					  
      <?php } ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="py-6 px-4 text-center text-gray-400">No user records found.</td>
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
    // Simple search for the users table
    document.getElementById('userSearch').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#usersTable tbody tr');
        rows.forEach(function(row) {
            let text = row.textContent.toLowerCase();
            row.style.display = text.indexOf(filter) > -1 ? '' : 'none';
        });
    });
    </script>
</body>
</html>
