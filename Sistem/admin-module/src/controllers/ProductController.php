<?php
class ProductController {
    private $products = [];

    public function addProduct($product) {
        $this->products[] = $product;
        return "Product added successfully.";
    }

    public function removeProduct($productId) {
        foreach ($this->products as $key => $product) {
            if ($product['id'] === $productId) {
                unset($this->products[$key]);
                return "Product removed successfully.";
            }
        }
        return "Product not found.";
    }

    public function listProducts() {
        return $this->products;
    }
}
?>