<?php
include('../inc/config.php');
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$success = '';
$error = '';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch event data
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();
$stmt->close();

if (!$event) {
    die("Event not found.");
}

// Handle form submission
if (isset($_POST['btnedit'])) {
    $event_name = trim($_POST['event_name']);
    $description = trim($_POST['description']);
    $venue = trim($_POST['venue']);
    $event_datetime = trim($_POST['event_datetime']);
    $status = isset($_POST['status']) ? $_POST['status'] : '';

    // Validate
    if (empty($event_name) || empty($venue) || empty($event_datetime) || empty($status)) {
        $error = "Event Name, Venue, Date/Time, and Status are required.";
    } else {
        // Update event
        $stmt = $conn->prepare("UPDATE events SET name=?, description=?, venue=?, event_datetime=?, status=? WHERE id=?");
        $stmt->bind_param("sssssi", $event_name, $description, $venue, $event_datetime, $status, $id);

        if ($stmt->execute()) {
            // Activity log (optional, if you have such a function)
            $operation = "updated event on $current_date";
            log_activity($conn, $manager_id, $role, $operation, $ip_address);

            // Refresh event data
            $event['name'] = $event_name;
            $event['description'] = $description;
            $event['venue'] = $venue;
            $event['event_datetime'] = $event_datetime;
            $event['status'] = $status;

            $success = "Event updated successfully.";
            header("Refresh:3; url=event-record.php");
        } else {
            $error = "Failed to update event.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Edit Event | <?php echo $app_name; ?></title>
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
                <h2 class="text-3xl font-bold text-indigo-700 mb-6">Edit Event</h2>
                <?php if ($success): ?>
                    <div class="mb-4 p-3 bg-green-100 text-green-700 rounded"><?php echo $success; ?></div>
                <?php elseif ($error): ?>
                    <div class="mb-4 p-3 bg-red-100 text-red-700 rounded"><?php echo $error; ?></div>
                <?php endif; ?>
                <form class="space-y-6" method="post" action="">
                    <div>
                        <label class="block text-gray-700 font-medium mb-1" for="event_name">Event Name</label>
                        <input type="text" id="event_name" name="event_name" value="<?php echo htmlspecialchars($event['name']); ?>" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1" for="description">Description</label>
                        <textarea id="description" name="description" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition" rows="4"><?php echo htmlspecialchars($event['description']); ?></textarea>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1" for="venue">Venue</label>
                        <input type="text" id="venue" name="venue" value="<?php echo htmlspecialchars($event['venue']); ?>" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1" for="event_datetime">Event Date & Time</label>
                        <input type="datetime-local" id="event_datetime" name="event_datetime" value="<?php echo date('Y-m-d\TH:i', strtotime($event['event_datetime'])); ?>" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1" for="status">Status</label>
                        <select id="status" name="status" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition" required>
                            <option value="">Select status</option>
                            <option value="upcoming" <?php if(isset($event['status']) && $event['status']=='upcoming') echo 'selected'; ?>>Upcoming</option>
                            <option value="cancelled" <?php if(isset($event['status']) && $event['status']=='cancelled') echo 'selected'; ?>>Cancelled</option>
                            <option value="completed" <?php if(isset($event['status']) && $event['status']=='completed') echo 'selected'; ?>>Completed</option>
                        </select>
                    </div>
                    <div>
                        <button type="submit" name="btnedit" class="w-full md:w-auto px-8 py-2 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition">Update Event</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <!-- Footer -->
    <footer class="py-6 text-center text-gray-600 bg-gray-100">
        <?php include('../partials/footer.php'); ?>
    </footer>
</body>
</html>
