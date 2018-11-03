<?php


namespace MysqlConnectStatus\Action;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use MysqlConnectStatus\Model\SyApiMysql;
use MysqlConnectStatus\Model\SyGameMysql;
use Zend\Diactoros\Response\JsonResponse;

class AuthBusinessAutograph implements RequestHandlerInterface
{
    private $pdoArray;

    public function __construct($pdoArray)
    {
        $this->pdoArray = $pdoArray;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $infomation = $this->message($this->pdoArray);
        return new JsonResponse(['info' => $infomation]);
    }

    protected function message($mysql)
    {
        $info = [];
        foreach ($mysql as $key => $value) {
            $code = 0;
            $message = '';
            $mysql = $key;
            $now = date('Y-m-d H:i:s');
            if (! $value) {
                $code = 500;
                $message = 'MYSQL连接失败！';
            }
            $info[] = [
                'code' => $code, 'message' => $message,
                'dependents' => ['mysql' => $mysql], 'env' => ['now' => $now]
            ];
        }
        return $info;
    }
}