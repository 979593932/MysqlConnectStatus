<?php


namespace MysqlConnectStatus\Factory;


use Interop\Container\ContainerInterface;
use PDO;
use Zend\Db\Exception\ErrorException;
use Zend\ServiceManager\Factory\FactoryInterface;

class BusinessFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $mysqlAdapters = $container->get('config')['db']['adapters'];
        $pdoArray = [];
        foreach ($mysqlAdapters as $key => $value) {
            $defaultOptions = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ];
            try {
                $pdo = new PDO(
                    $value['dsn'],
                    $value['username'],
                    $value['password'],
                    array_merge($defaultOptions, $value['options'] ?? [])
                );
            } catch (\PDOException $exception) {
                throw new ErrorException('Unable to connect to db server. Error:' . $exception->getMessage(), 31);
            }

            try {
                $pdo::ATTR_SERVER_INFO;
            } catch (\PDOException $exception) {
                if (strpos($exception->getMessage(), 'MySQL server has gone away') !== false) {
                    $pdoArray[$key] = false;
                }
            }
            $pdoArray[$key] = true;
        }
        return new $requestedName($pdoArray);
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