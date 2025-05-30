<?php
class Supplier {
    private $id;
    private $name;
    private $contactInfo;

    public function __construct($id, $name, $contactInfo) {
        $this->id = $id;
        $this->name = $name;
        $this->contactInfo = $contactInfo;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getContactInfo() {
        return $this->contactInfo;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setContactInfo($contactInfo) {
        $this->contactInfo = $contactInfo;
    }
}
?>