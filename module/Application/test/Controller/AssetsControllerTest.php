<?php
namespace ApplicationTest\Controller;

use Application\Controller\IndexController;
use Zend\Stdlib\ArrayUtils;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class AssetControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(ArrayUtils::merge(
            include __DIR__ . '/../../../../config/application.config.php',
            []
        ));

        parent::setUp();
    }

    public function helpTest_indexAction_filters()
    {
        return [
            ['', '[]'],
            ['ERROR', '[{"id":"181c5e36-05ae-42aa-89c7-c42a24934660","status":{"id":"d4250e33-f81f-408f-855a-36414fe4cee5","assetId":"181c5e36-05ae-42aa-89c7-c42a24934660","statusType":"ERROR","createdAt":"2010-09-07T01:48:20Z"}}]']
        ];
    }

    /**
     * @dataProvider helpTest_indexAction_filters
     */
    public function test_indexAction_statusFilter($filter, $expectedAssets)
    {
        $this->dispatch("/assets?status={$filter}");
        $this->assertResponseStatusCode(200);
        $this->assertEquals(
            $expectedAssets,
            $this->getResponse()->getBody()
        );
    }

    public function test_indexAction_empty()
    {
        $this->dispatch("/assets");
        $this->assertResponseStatusCode(200);
        $this->assertEquals(
            json_encode([]),
            $this->getResponse()->getBody()
        );
    }

    public function helpTest_getByIdAction_items()
    {
        return [
            ['179ad179-19d2-4623-97ad-49a8584d1705']
        ];
    }

    /**
     * @dataProvider helpTest_getByIdAction_items
     */
    public function test_getByIdAction($id)
    {
        $this->dispatch("/assets/{$id}");
        $this->assertResponseStatusCode(200);
    }

    public function test_getByIdAction_notFound()
    {
        $this->dispatch("/assets/i-am-the-very-model-of-the-modern-major-general");
        $this->assertResponseStatusCode(404);
    }

    /**
     * @dataProvider helpTest_indexAction_filters
     */
    // public function test_indexAction_noDb($filter, $unused)
    // {
    //     $this->setApplicationConfig(ArrayUtils::merge(
    //         include __DIR__ . '/../../../../config/application.config.php',
    //         ['db' => []]
    //     ));

    //     $this->dispatch("/assets?status={$filter}");
    //     $this->assertResponseStatusCode(500);
    // }
}
