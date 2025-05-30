<?php
class InventoryController {
    private $inventoryModel;

    public function __construct() {
        // Assuming Inventory model is included and instantiated here
        $this->inventoryModel = new Inventory();
    }

    public function checkInventory($productId) {
        // Logic to check inventory for a specific product
        return $this->inventoryModel->getInventoryByProductId($productId);
    }

    public function updateInventory($productId, $quantity) {
        // Logic to update inventory for a specific product
        return $this->inventoryModel->updateInventory($productId, $quantity);
    }

    public function listInventory() {
        // Logic to list all inventory items
        return $this->inventoryModel->getAllInventory();
    }
}
?>