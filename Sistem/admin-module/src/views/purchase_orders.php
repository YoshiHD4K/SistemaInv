<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Orders Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Manage Purchase Orders</h1>
        
        <form id="createOrderForm">
            <h2>Create Purchase Order</h2>
            <label for="supplierId">Supplier:</label>
            <select id="supplierId" required>
                <!-- Options will be populated dynamically -->
            </select>
            <label for="productIds">Products:</label>
            <select id="productIds" multiple required>
                <!-- Options will be populated dynamically -->
            </select>
            <button type="submit">Create Order</button>
        </form>

        <h2>Existing Purchase Orders</h2>
        <table id="ordersTable">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Supplier</th>
                    <th>Products</th>
                    <th>Order Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Existing orders will be populated dynamically -->
            </tbody>
        </table>
    </div>
    <script src="../assets/js/main.js"></script>
</body>
</html>