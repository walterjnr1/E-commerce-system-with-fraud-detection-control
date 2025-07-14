<?php
include('../inc/config.php');
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
}
$success = '';
$error = '';

// Handle form submission
if (isset($_POST['btnprofile'])) {
    $new_name = trim($_POST['name']);
    $new_email = trim($_POST['email']);
    $new_phone = trim($_POST['phone']);
  
    // Validate
    if (empty($new_name) || empty($new_email)) {
        $error = "Name and Email are required.";
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    
    } else {
        // Check if email is taken by another user
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $new_email, $user_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = "Email already in use.";
        } else {
            // Update user
            
                $stmt = $conn->prepare("UPDATE users SET full_name=?, email=?, phone=? WHERE id=?");
                $stmt->bind_param("sssi", $new_name, $new_email, $new_phone, $user_id);
            }
            if ($stmt->execute()) {

                $stmt->close();

                //activity log
                $operation = "updated profile on $current_date";
                log_activity($conn, $user_id, $role, $operation, $ip_address);

                //redirect to profile page after 2 seconds
                header("Refresh:2; url=profile.php");

                $success = "Profile updated successfully.";
                $name = $new_name;
                $email = $new_email;
                $phone = $new_phone;
            } else {
                $error = "Failed to update profile.";
                $stmt->close();
            }
        }
    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>My Profile | <?php echo $app_name; ?></title>
    <?php include('partials/head.php'); ?>
</head>
<body class="bg-gray-50 text-gray-800">
    <!-- Navbar -->
    <header class="fixed top-0 left-0 w-full bg-white shadow z-50">
         <?php include('partials/navbar.php'); ?>
    </header>

    <section class="min-h-screen flex items-start justify-center pt-24 pb-12">
        <!-- Main Dashboard Container -->
        <div class="w-full max-w-6xl mx-auto flex flex-col md:flex-row gap-8">
            <!-- Left Sidebar Menu -->
            <aside class="w-full md:w-72 bg-white rounded-2xl shadow-2xl p-6 flex flex-col gap-2 sticky top-28 z-10 md:order-1 order-2 mb-8 md:mb-0">
                <?php include('partials/sidebar.php'); ?>
            </aside>
            <!-- Main Content -->
            <div class="flex-1 bg-white rounded-2xl shadow-2xl p-8 mb-8 md:mb-0 md:order-2 order-1">
                <h2 class="text-3xl font-bold text-indigo-700 mb-6">Profile</h2>
                <?php if ($success): ?>
                    <div class="mb-4 p-3 bg-green-100 text-green-700 rounded"><?php echo $success; ?></div>
                <?php elseif ($error): ?>
                    <div class="mb-4 p-3 bg-red-100 text-red-700 rounded"><?php echo $error; ?></div>
                <?php endif; ?>
                <div class="flex flex-col md:flex-row gap-8">
                    <!-- Profile Info -->
                    <div class="flex flex-col items-center md:items-start md:w-1/3">
                        <div class="w-28 h-28 rounded-full bg-indigo-100 flex items-center justify-center text-4xl font-bold text-indigo-700 mb-4">
                            <?php echo strtoupper(substr($row_user['full_name'], 0, 1)); ?>
                        </div>
                        <div class="mb-2 text-xl font-semibold"><?php echo htmlspecialchars($row_user['full_name']); ?></div>
                        <div class="mb-2 text-gray-500"><?php echo htmlspecialchars($row_user['email']); ?></div>
                        <div class="mb-2 text-gray-400 text-sm">Role: <?php echo htmlspecialchars($row_user['role']); ?></div>
                    </div>
                    <!-- Profile Edit Form -->
                    <form class="flex-1 space-y-6" method="post" action="">
                        <div>
                            <label class="block text-gray-700 font-medium mb-1" for="name">Full Name</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($row_user['full_name']); ?>" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-1" for="email">Email Address</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($row_user['email']); ?>" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-1" for="phone">Phone</label>
                            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($row_user['phone']); ?>" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition" required>
                        </div>
                       
                        <div>
                            <button type="submit" name="btnprofile" class="w-full md:w-auto px-8 py-2 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition">Update Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- Footer -->
    <footer class="py-6 text-center text-gray-600 bg-gray-100">
        <?php include('../partials/footer.php'); ?>
    </footer>
</body>
</html>
