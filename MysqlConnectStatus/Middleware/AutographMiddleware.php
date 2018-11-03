<?php


namespace MysqlConnectStatus\Middleware;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;

class AutographMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $params = $request->getQueryParams();
        $headers = $request->getHeaders();
        $timestamp = $headers['timestamp'] ?? '';
        $authorization = $headers['authorization'] ?? '';
        $key = 'Tt5aTLS3ByE53PWZzUab';
        /*$authorization = 'd26ba2dae0efe53fdd74d5f7d02d38ab';
          $timestamp = '20181102';*/
        $data = implode($params) . $timestamp . $key;
        $hash = md5($data);
        if ($hash != $authorization) {
            return new JsonResponse(['detail' => '签名认证失败'], 403);
        }
        return $handler->handle($request);
    }
}