<?php
class PurchaseOrderController {
    private $purchaseOrderModel;

    public function __construct() {
        $this->purchaseOrderModel = new PurchaseOrder();
    }

    public function createOrder($supplierId, $productIds) {
        // Logic to create a new purchase order
        $orderDate = date('Y-m-d H:i:s');
        return $this->purchaseOrderModel->create($supplierId, $productIds, $orderDate);
    }

    public function listOrders() {
        // Logic to list all purchase orders
        return $this->purchaseOrderModel->getAll();
    }

    public function updateOrder($orderId, $supplierId, $productIds) {
        // Logic to update an existing purchase order
        return $this->purchaseOrderModel->update($orderId, $supplierId, $productIds);
    }
}
?>