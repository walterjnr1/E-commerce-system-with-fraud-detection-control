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

// Count total products
$count_sql = "SELECT COUNT(id) as total FROM products";
$count_result = $conn->query($count_sql);
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

// Fetch products
$sql = "SELECT id, name, description, image, price, stock_qty FROM products ORDER BY id DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Records | <?php echo $app_name; ?></title>
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
                <h2 class="text-3xl font-bold text-indigo-700 mb-6">Product Records</h2>

                <!-- Search Bar -->
                <div class="mb-6 flex flex-col md:flex-row gap-3 md:gap-4 items-center">
                    <input id="productSearch" type="text" placeholder="Search by any column..." class="w-full md:w-80 px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition" autocomplete="off" />
                </div>

                <!-- Products Table -->
                <div class="overflow-x-auto rounded-lg shadow">
                    <table class="min-w-full bg-white" id="productsTable">
                        <thead class="bg-indigo-50">
                            <tr>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">S/N</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Image</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Product Name</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Description</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700">Price ($)</th>
                                <th class="py-3 px-4 text-center font-semibold text-gray-700">Stock</th>
                                <th class="py-3 px-4 text-center font-semibold text-gray-700">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($products) > 0): ?>
                                <?php foreach ($products as $index => $product): ?>
                                    <tr class="border-b hover:bg-indigo-50 transition">
                                        <td class="py-3 px-4 font-mono text-indigo-700"><?php echo (($page - 1) * $limit) + $index + 1; ?></td>
                                        <td class="py-3 px-4">
                                            <?php if (!empty($product['image'])): ?>
                                                <img src="../<?php echo htmlspecialchars($product['image']); ?>" alt="Product Image" class="w-16 h-16 object-cover rounded-lg border">
                                            <?php else: ?>
                                                <span class="text-gray-400 italic">No Image</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($product['name']); ?></td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($product['description']); ?></td>
                                        <td class="py-3 px-4">N<?php echo number_format($product['price'], 2); ?></td>
                                        <td class="py-3 px-4 text-center">
                                            <?php if ($product['stock_qty'] < 10): ?>
                                                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                                                    Low (<?php echo $product['stock_qty']; ?>)
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                                    <?php echo $product['stock_qty']; ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <a href="edit-product.php?id=<?php echo $product['id']; ?>" class="text-indigo-600 hover:text-indigo-900 mr-3" title="Edit Product">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="delete-product.php?id=<?php echo $product['id']; ?>" class="text-red-600 hover:text-red-800" title="Delete Product" onclick="return confirm('Are you sure you want to delete this product?');">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="py-6 px-4 text-center text-gray-400">No product records found.</td>
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
    // Search filter
    document.getElementById('productSearch').addEventListener('keyup', function () {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#productsTable tbody tr');
        rows.forEach(function (row) {
            let text = row.textContent.toLowerCase();
            row.style.display = text.indexOf(filter) > -1 ? '' : 'none';
        });
    });
    </script>
</body>
</html>
