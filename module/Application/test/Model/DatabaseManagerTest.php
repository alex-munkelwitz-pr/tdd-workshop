<?php
namespace ApplicationTest\Model;

use PHPUnit\Framework\TestCase;
use \Application\Model\Asset;
use \Application\Model\AssetStatus;

class DatabaseManagerTest extends TestCase
{
    public function helpTest_fetchByStatusType_data()
    {
        return [
            [
                'ERROR',
                [
                    [
                        "id" => "645b54d9-a469-4e0a-8115-07adef7e14d9",
                        "status" => [
                            "id" => "4f436a0d-903b-46bd-8e45-46c1dd3dc631",
                            "assetId" => "645b54d9-a469-4e0a-8115-07adef7e14d9",
                            "statusType" => "ERROR",
                            "createdAt" => "2010-02-14T17 => 06 => 46Z"
                        ]
                    ],
                    [
                        "id" => "c55d52cb-9e06-46b6-b093-bbef6de76470",
                        "status" => [
                            "id" => "c3ee6d5b-7652-4428-9fe3-842641ba51b6",
                            "assetId" => "c55d52cb-9e06-46b6-b093-bbef6de76470",
                            "statusType" => "ERROR",
                            "createdAt" => "2010-02-23T22 => 27 => 59Z"
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @dataProvider helpTest_fetchByStatusType_data
     */
    public function test_fetchByStatusType_data($statusType, array $expectedAssetStatusRows)
    {
        $prophet = new \Prophecy\Prophet;

        $execCount = -1;
        $pdoStatement = $prophet->prophesize(\PDOStatement::class);
        $pdoStatement->fetch(\PDO::FETCH_NUM)
            ->shouldBeCalledTimes(3)
            ->will(function() use ($expectedAssetStatusRows, &$execCount) {
                if (++$execCount === 2) {
                    return false;
                }
                return [
                    $expectedAssetStatusRows[$execCount]['status']['id'],
                    $expectedAssetStatusRows[$execCount]['status']['assetId'],
                    $expectedAssetStatusRows[$execCount]['status']['statusType'],
                    $expectedAssetStatusRows[$execCount]['status']['createdAt']
                ];
            });

        $handle = $prophet->prophesize(\PDO::class);
        $handle->query("SELECT id, asset_id, status_type, created_at FROM asset_statuses WHERE status_type='{$statusType}'")
            ->willReturn($pdoStatement)
            ->shouldBeCalledTimes(1);

        $databaseManager = new \Application\Model\DatabaseManager($handle->reveal());

        $assets = [];
        foreach ($databaseManager->fetchByStatusType($statusType) as $asset) {
            $assets[] = $asset;
        }
        $this->assertEquals(
            array_map(
                function($thing) {
                    return new Asset(
                        $thing['id'],
                        new AssetStatus(
                            $thing['status']['id'],
                            $thing['status']['assetId'],
                            $thing['status']['statusType'],
                            $thing['status']['createdAt']
                        )
                    );
                },
                $expectedAssetStatusRows
            ),
            $assets
        );
    }

}
