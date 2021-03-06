<?php

namespace Application\Model;

class Asset implements \JsonSerializable {

    private $id;
    private $status;

    public function __construct($id, AssetStatus $status = null) {
        $this->id = $id;
        $this->status = $status;
    }

    public function getId() {
        return $this->id;
    }

    public function getStatus() {
        return $this->status;
    }

    public function jsonSerialize() {
        return [
            'id' => $this->getId(),
            'status' => $this->getStatus()->jsonSerialize()
        ];
    }
}