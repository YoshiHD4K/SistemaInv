# Admin Module for Inventory Management System

## Overview
This project is an admin module designed for managing suppliers, products, inventory, and purchase orders within an inventory management system. It provides a user-friendly interface for administrators to perform various operations related to suppliers and products.

## Features
- **Supplier Management**: Add, remove, and list suppliers.
- **Product Management**: Add, remove, and list products.
- **Inventory Management**: Check and update inventory levels.
- **Purchase Order Management**: Create and list purchase orders.

## Project Structure
```
admin-module
├── src
│   ├── controllers
│   │   ├── SupplierController.php
│   │   ├── ProductController.php
│   │   ├── InventoryController.php
│   │   └── PurchaseOrderController.php
│   ├── models
│   │   ├── Supplier.php
│   │   ├── Product.php
│   │   ├── Inventory.php
│   │   └── PurchaseOrder.php
│   ├── views
│   │   ├── suppliers.php
│   │   ├── products.php
│   │   ├── inventory.php
│   │   └── purchase_orders.php
│   ├── routes
│   │   └── admin_routes.php
│   └── utils
│       └── db.php
├── public
│   ├── index.php
│   └── assets
│       ├── css
│       │   └── style.css
│       └── js
│           └── main.js
├── config
│   └── config.php
└── README.md
```

## Installation
1. Clone the repository to your local machine.
2. Navigate to the project directory.
3. Set up your database and update the configuration in `config/config.php`.
4. Run the application by accessing `public/index.php` in your web browser.

## Usage
- Access the admin module through the main application interface.
- Use the navigation to manage suppliers, products, inventory, and purchase orders.
- Follow the on-screen instructions to perform actions such as adding or removing entries.

## Contributing
Contributions are welcome! Please submit a pull request or open an issue for any enhancements or bug fixes.

## License
This project is licensed under the MIT License. See the LICENSE file for more details.