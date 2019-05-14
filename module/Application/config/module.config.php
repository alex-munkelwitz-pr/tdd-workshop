<?php

namespace Application;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Method;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'assets' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/assets',
                    'defaults' => [
                        'controller' => Controller\AssetsController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'assetsById' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/assets/:id',
                    'defaults' => [
                        'controller' => Controller\AssetsController::class,
                        'action'     => 'getById',
                    ],
                ],
            ],
            'saveAssetStatus' => [
                'type'    => Method::class,
                'options' => [
                    'route'    => '/asset-statuses',
                    'verb' => 'post',
                    'defaults' => [
                        'controller' => Controller\AssetStatusesController::class,
                        'action'     => 'saveAssetStatus',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
            Controller\AssetsController::class => function($container) {
                return new Controller\AssetsController(
                    $container->get(\PDO::class),
                    $container->get(\Application\Model\DatabaseManager::class)
                );
            },
            Controller\AssetStatusesController::class => function($container) {
                return new Controller\AssetStatusesController(
                    $container->get(\PDO::class),
                    $container->get(Service\SocketManager::class)
                );
            },
        ],
    ],
    'service_manager' => [
        'factories' => [
            \PDO::class => function ($container) {
              $dbConfig = $container->get('config')['db'];
              $host = $dbConfig['host'];
              $port = $dbConfig['port'];
              $dbName = $dbConfig['dbname'];
              $dbUser = $dbConfig['dbuser'];
              $dbPassword = $dbConfig['dbpassword'];
              return new \PDO("pgsql:host={$host};port={$port};dbname={$dbName}", $dbUser, $dbPassword);
            },
            Service\SocketManager::class => InvokableFactory::class,
            Model\DatabaseManager::class => function($container) {
                return new Model\DatabaseManager(
                    $container->get(\PDO::class),
                    [Model\Asset::class, 'fromAssociativeArray']
                );
            }
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'strategies' => [
            'ViewJsonStrategy',
        ],
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
