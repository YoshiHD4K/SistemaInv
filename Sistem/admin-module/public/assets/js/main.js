// This file contains JavaScript functions for enhancing the interactivity of the admin module views.

document.addEventListener('DOMContentLoaded', function() {
    // Function to add a supplier
    document.getElementById('addSupplierForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(this);
        fetch('path/to/supplier/add', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Supplier added successfully!');
                // Refresh supplier list or update UI accordingly
            } else {
                alert('Error adding supplier: ' + data.message);
            }
        });
    });

    // Function to remove a supplier
    document.querySelectorAll('.removeSupplierBtn').forEach(button => {
        button.addEventListener('click', function() {
            const supplierId = this.dataset.id;
            fetch(`path/to/supplier/remove/${supplierId}`, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Supplier removed successfully!');
                    // Refresh supplier list or update UI accordingly
                } else {
                    alert('Error removing supplier: ' + data.message);
                }
            });
        });
    });

    // Function to add a product
    document.getElementById('addProductForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(this);
        fetch('path/to/product/add', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Product added successfully!');
                // Refresh product list or update UI accordingly
            } else {
                alert('Error adding product: ' + data.message);
            }
        });
    });

    // Function to remove a product
    document.querySelectorAll('.removeProductBtn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.id;
            fetch(`path/to/product/remove/${productId}`, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Product removed successfully!');
                    // Refresh product list or update UI accordingly
                } else {
                    alert('Error removing product: ' + data.message);
                }
            });
        });
    });

    // Function to update inventory
    document.getElementById('updateInventoryForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(this);
        fetch('path/to/inventory/update', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Inventory updated successfully!');
                // Refresh inventory list or update UI accordingly
            } else {
                alert('Error updating inventory: ' + data.message);
            }
        });
    });

    // Function to create a purchase order
    document.getElementById('createOrderForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(this);
        fetch('path/to/purchase/order/create', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Purchase order created successfully!');
                // Refresh order list or update UI accordingly
            } else {
                alert('Error creating purchase order: ' + data.message);
            }
        });
    });
});