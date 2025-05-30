<?php
require_once '../controllers/SupplierController.php';
require_once '../controllers/ProductController.php';
require_once '../controllers/InventoryController.php';
require_once '../controllers/PurchaseOrderController.php';

$router = new Router();

$router->get('/suppliers', function() {
    $controller = new SupplierController();
    $controller->listSuppliers();
});

$router->post('/suppliers/add', function() {
    $controller = new SupplierController();
    $controller->addSupplier();
});

$router->post('/suppliers/remove', function() {
    $controller = new SupplierController();
    $controller->removeSupplier();
});

$router->get('/products', function() {
    $controller = new ProductController();
    $controller->listProducts();
});

$router->post('/products/add', function() {
    $controller = new ProductController();
    $controller->addProduct();
});

$router->post('/products/remove', function() {
    $controller = new ProductController();
    $controller->removeProduct();
});

$router->get('/inventory', function() {
    $controller = new InventoryController();
    $controller->listInventory();
});

$router->post('/inventory/update', function() {
    $controller = new InventoryController();
    $controller->updateInventory();
});

$router->get('/purchase-orders', function() {
    $controller = new PurchaseOrderController();
    $controller->listOrders();
});

$router->post('/purchase-orders/create', function() {
    $controller = new PurchaseOrderController();
    $controller->createOrder();
});

$router->post('/purchase-orders/update', function() {
    $controller = new PurchaseOrderController();
    $controller->updateOrder();
});
?>