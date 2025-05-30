<?php
class Inventory {
    private $productId;
    private $quantity;
    private $location;

    public function __construct($productId, $quantity, $location) {
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->location = $location;
    }

    public function getProductId() {
        return $this->productId;
    }

    public function setProductId($productId) {
        $this->productId = $productId;
    }

    public function getQuantity() {
        return $this->quantity;
    }

    public function setQuantity($quantity) {
        $this->quantity = $quantity;
    }

    public function getLocation() {
        return $this->location;
    }

    public function setLocation($location) {
        $this->location = $location;
    }
}
?>