<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Inventory Management</h1>
        
        <div class="inventory-form">
            <h2>Update Inventory</h2>
            <form id="formUpdateInventory">
                <label for="productId">Product ID:</label>
                <input type="text" id="productId" required>
                
                <label for="quantity">Quantity:</label>
                <input type="number" id="quantity" required>
                
                <button type="submit">Update Inventory</button>
            </form>
        </div>

        <div class="inventory-list">
            <h2>Current Stock Levels</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Location</th>
                    </tr>
                </thead>
                <tbody id="inventoryTableBody">
                    <!-- Inventory items will be dynamically populated here -->
                </tbody>
            </table>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
</body>
</html>