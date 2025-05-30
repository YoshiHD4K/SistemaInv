<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Suppliers</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Manage Suppliers</h1>
        
        <form id="addSupplierForm">
            <h2>Add Supplier</h2>
            <input type="text" id="supplierName" placeholder="Supplier Name" required>
            <input type="text" id="supplierContact" placeholder="Contact Information" required>
            <button type="submit">Add Supplier</button>
        </form>

        <form id="removeSupplierForm">
            <h2>Remove Supplier</h2>
            <input type="text" id="supplierId" placeholder="Supplier ID" required>
            <button type="submit">Remove Supplier</button>
        </form>

        <h2>Supplier List</h2>
        <div id="supplierList">
            <!-- Supplier list will be populated here -->
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
</body>
</html>