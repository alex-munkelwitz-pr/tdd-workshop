<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

use Application\Model\Asset;
use Application\Model\AssetStatus;

class AssetsController extends AbstractActionController
{
    private $db, $databaseManager;

    public function __construct(\PDO $db, \Application\Model\DatabaseManager $databaseManager) {
        $this->db = $db;
        $this->databaseManager = $databaseManager;
    }

    public function indexAction() {
        $statusType = $this->params()->fromQuery('status');
        $assets = [];
        foreach ($this->databaseManager->fetchByStatusType($statusType) as $assetStatus) {
            $assets[] = new Asset(
                $assetStatus->getAssetId(),
                $assetStatus
            );
        }
        return new JsonModel($assets);
    }

    public function getByIdAction() {
        $assetId = $this->params('id');
        $result = $this->db->query("SELECT id, asset_id, status_type, created_at FROM asset_statuses WHERE asset_id='{$assetId}'");
        if ($result->rowCount()) {
            $row = $result->fetch(\PDO::FETCH_NUM);
            $assetStatus = new AssetStatus(...$row);
            return new JsonModel((new Asset($assetId, $assetStatus))->jsonSerialize());
        } else {
            $this->response->setStatusCode(404);
        }
    }

    private function fetchByStatusType($statusType)
    {
        $result = $this->db->query("SELECT id, asset_id, status_type, created_at FROM asset_statuses WHERE status_type='{$statusType}'");
        while ($row = $result->fetch(\PDO::FETCH_NUM)) {
            yield new AssetStatus(...$row);
        }
    }
}
