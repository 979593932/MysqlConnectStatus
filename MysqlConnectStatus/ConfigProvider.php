<?php


namespace MysqlConnectStatus;


use MysqlConnectStatus\Factory\BusinessFactory;
use MysqlConnectStatus\Middleware\AutographMiddleware;
use MysqlConnectStatus\Action\AuthBusinessAutograph;


class ConfigProvider
{
    public function __invoke()
    {
        return [
            'dependencies' => [
                'factories' => [
                    AuthBusinessAutograph::class => BusinessFactory::class,
                ]
            ],
            'routes' => [
                /**
                 * 主动业务巡检验证签名
                 */
                [
                    'name' => 'business-auth',
                    'path' => '/business/auth',
                    'middleware' => [
                        AutographMiddleware::class,
                        AuthBusinessAutograph::class,
                    ],
                    'allowed_methods' => ['GET'],
                ],
            ]
        ];
    }
}