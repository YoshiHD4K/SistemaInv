<?php
class SupplierController {
    private $suppliers = [];

    public function addSupplier($name, $contact) {
        $supplierId = count($this->suppliers) + 1;
        $this->suppliers[$supplierId] = [
            'id' => $supplierId,
            'name' => $name,
            'contact' => $contact
        ];
        return $supplierId;
    }

    public function removeSupplier($id) {
        if (isset($this->suppliers[$id])) {
            unset($this->suppliers[$id]);
            return true;
        }
        return false;
    }

    public function listSuppliers() {
        return $this->suppliers;
    }
}
?>