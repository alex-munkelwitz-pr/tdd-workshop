<?php

namespace Application\Model;

class DatabaseManager
{
    private $db;
    private $assetBuilder;

    public function __construct(\PDO $db, $assetBuilder) {
        $this->db = $db;
        $this->assetBuilder = $assetBuilder;
    }

    public function fetchByStatusType($statusType)
    {
        $result = $this->db->query("SELECT id, asset_id, status_type, created_at FROM asset_statuses WHERE status_type='{$statusType}'");
        while ($result && $row = $result->fetch(\PDO::FETCH_ASSOC)) {
            yield call_user_func($this->assetBuilder, $row);
        }
    }
}
