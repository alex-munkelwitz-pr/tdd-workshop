<?php

namespace Application\Model;

class AssetStatus implements \JsonSerializable {
    private $id;
    private $assetId;
    private $statusType;
    private $createdAt;

    public function __construct($id, $assetId, $statusType, $createdAt) {
        $this->id = $id;
        $this->assetId = $assetId;
        $this->statusType = $statusType;
        $this->createdAt = $createdAt;
    }

    public static function fromAssociativeArray($row)
    {
        return new self($row['id'], $row['asset_id'], $row['status_type'], $row['created_at']);
    }

    public function getId() {
        return $this->id;
    }

    public function getAssetId() {
        return $this->assetId;
    }

    public function getStatusType() {
        return $this->statusType;
    }

    public function getCreatedAt() {
        return $this->createdAt;
    }

    public function jsonSerialize() {
        return [
            'id' => $this->getId(),
            'assetId' => $this->getAssetId(),
            'statusType' => $this->getStatusType(),
            'createdAt' => $this->getCreatedAt()
        ];
    }
}
