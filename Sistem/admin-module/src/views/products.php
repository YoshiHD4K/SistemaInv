<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Manage Products</h1>
        
        <form id="formAddProduct" method="POST" action="../controllers/ProductController.php?action=add">
            <h2>Add Product</h2>
            <input type="text" name="productName" placeholder="Product Name" required>
            <input type="number" name="productPrice" placeholder="Product Price" required>
            <input type="number" name="productStock" placeholder="Stock Quantity" required>
            <button type="submit">Add Product</button>
        </form>

        <form id="formRemoveProduct" method="POST" action="../controllers/ProductController.php?action=remove">
            <h2>Remove Product</h2>
            <input type="number" name="productId" placeholder="Product ID" required>
            <button type="submit">Remove Product</button>
        </form>

        <h2>Product List</h2>
        <div id="productList">
            <!-- Product list will be populated here by the server -->
        </div>
    </div>
    <script src="../assets/js/main.js"></script>
</body>
</html>