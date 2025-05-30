<?php
class PurchaseOrder {
    private $id;
    private $supplierId;
    private $productIds;
    private $orderDate;

    public function __construct($id, $supplierId, $productIds, $orderDate) {
        $this->id = $id;
        $this->supplierId = $supplierId;
        $this->productIds = $productIds;
        $this->orderDate = $orderDate;
    }

    public function getId() {
        return $this->id;
    }

    public function getSupplierId() {
        return $this->supplierId;
    }

    public function getProductIds() {
        return $this->productIds;
    }

    public function getOrderDate() {
        return $this->orderDate;
    }

    public function setSupplierId($supplierId) {
        $this->supplierId = $supplierId;
    }

    public function setProductIds($productIds) {
        $this->productIds = $productIds;
    }

    public function setOrderDate($orderDate) {
        $this->orderDate = $orderDate;
    }
}
?>