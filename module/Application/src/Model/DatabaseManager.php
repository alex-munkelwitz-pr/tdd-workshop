<?php

namespace Application\Model;

class DatabaseManager
{
    private $db;

    public function __construct(\PDO $db) {
        $this->db = $db;
    }

    public function fetchByStatusType($statusType)
    {
        $result = $this->db->query("SELECT id, asset_id, status_type, created_at FROM asset_statuses WHERE status_type='{$statusType}'");
        while ($result && $row = $result->fetch(\PDO::FETCH_NUM)) {
            yield new Asset($row[1], new AssetStatus(...$row));
        }
    }
}
