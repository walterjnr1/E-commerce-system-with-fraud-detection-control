<?php
include('../inc/config.php');
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
}

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnadd'])) {
    // Collect form data
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $stock_qty = trim($_POST['stock_qty']);

    // Handle image upload
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp','image/jpg'];
        $max_size = 2 * 1024 * 1024; // 2MB

        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $error = "Error uploading image.";
        } elseif (!in_array($_FILES['image']['type'], $allowed_types)) {
            $error = "Only JPEG, PNG, GIF,  WEBP and jpg images are allowed.";
        } elseif ($_FILES['image']['size'] > $max_size) {
            $error = "Image size must be less than 2MB.";
        } else {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $new_name = uniqid('prod_', true) . '.' . $ext;
            $upload_dir = '../uploadImage/products/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $target = $upload_dir . $new_name;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $image_path = 'uploadImage/products/' . $new_name;
            } else {
                $error = "Failed to save uploaded image.";
            }
        }
    }

    // Validate
    if (empty($name) || empty($price) || empty($stock_qty)) {
        $error = "Product name, price, and stock quantity are required.";
    } elseif (!is_numeric($price) || $price < 0) {
        $error = "Price must be a non-negative number.";
    } elseif (!is_numeric($stock_qty) || $stock_qty < 0) {
        $error = "Stock quantity must be a non-negative number.";
    } else {
        // Check if product already exists
        $stmt = $conn->prepare("SELECT id FROM products WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = "Product with this name already exists.";
        }
        $stmt->close();
    }

    if (!$error) {
        // Insert into products table
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock_qty, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdis", $name, $description, $price, $stock_qty, $image_path);
        if ($stmt->execute()) {
            $success = true;

            //activity log
            $operation = "added product '$name' on " . date('Y-m-d H:i:s');
            log_activity($conn, $user_id, $role, $operation, $ip_address);

        } else {
            $error = "Failed to add product. Please try again.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Add Product | <?php echo $app_name ?? 'E-Commerce'; ?></title>
    <?php include('partials/head.php'); ?>
</head>
<body class="bg-gray-50 text-gray-800">
    <!-- Navbar -->
    <header class="fixed top-0 left-0 w-full bg-white shadow z-50">
         <?php include('partials/navbar.php'); ?>
    </header>

    <section class="min-h-screen flex items-start justify-center pt-24 pb-12">
        <div class="w-full max-w-6xl mx-auto flex flex-col md:flex-row gap-8">
            <aside class="w-full md:w-72 bg-white rounded-2xl shadow-2xl p-6 flex flex-col gap-2 sticky top-28 z-10 md:order-1 order-2 mb-8 md:mb-0">
                <?php include('partials/sidebar.php'); ?>
            </aside>
            <div class="flex-1 bg-white rounded-2xl shadow-2xl p-8 mb-8 md:mb-0 md:order-2 order-1">
                <h2 class="text-3xl font-bold text-indigo-700 mb-6">Add Product</h2>
                <?php if ($success): ?>
                    <div class="mb-6 p-4 bg-green-100 border border-green-300 text-green-800 rounded">Product added successfully!</div>
                <?php elseif ($error): ?>
                    <div class="mb-6 p-4 bg-red-100 border border-red-300 text-red-800 rounded"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <form class="space-y-8" method="post" action="" autocomplete="off" enctype="multipart/form-data">
                    <div>
                        <label class="block text-gray-700 font-medium mb-1" for="name">Product Name</label>
                        <input type="text" id="name" name="name" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1" for="description">Description</label>
                        <textarea id="description" name="description" maxlength="50" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition"></textarea>
                        <p class="text-xs text-gray-500 mt-1">Max 50 characters.</p>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1" for="price">Price</label>
                        <input type="number" id="price" name="price" min="0" step="0.01" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1" for="stock_qty">Stock Quantity</label>
                        <input type="number" id="stock_qty" name="stock_qty" min="0" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1" for="image">Product Image</label>
                        <input type="file" id="image" name="image" accept="image/*" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition">
                        <p class="text-xs text-gray-500 mt-1">Max size: 2MB. Allowed: JPG, PNG, GIF, WEBP.</p>
                    </div>
                    <div>
                        <button type="submit" name="btnadd" class="w-full md:w-auto px-8 py-2 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition">Add Product</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <footer class="py-6 text-center text-gray-600 bg-gray-100">
        <?php include('../partials/footer.php'); ?>
    </footer>
</body>
</html>
