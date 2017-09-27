<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-08-26 13:55
 */
namespace Notadd\Administration;

use Illuminate\Container\Container;
use Illuminate\Database\DatabaseManager;
use PDO;

/**
 * Class SystemInformation.
 */
class SystemInformation
{
    /**
     * @var \Illuminate\Container\Container|\Notadd\Foundation\Application
     */
    private $container;

    /**
     * @var \Illuminate\Database\DatabaseManager
     */
    private $database;

    /**
     * SystemInformation constructor.
     *
     * @param \Illuminate\Container\Container      $container
     * @param \Illuminate\Database\DatabaseManager $database
     */
    public function __construct(Container $container, DatabaseManager $database)
    {
        $this->container = $container;
        $this->database = $database;
    }

    /**
     * @return array
     */
    public function handler()
    {
        $pdo = $this->database->connection()->getPdo();
        return [
            [
                'tag'=> 'p',
                'content' => [
                    'Notadd 版本：' . $this->container->version(),
                ],
            ],
            [
                'tag'=> 'p',
                'content' => [
                    '系统版本：' . php_uname('s') . ' ' . php_uname('r'),
                ],
            ],
            [
                'tag'=> 'p',
                'content' => [
                    '数据库版本：' . $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) . ' ' . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION),
                ],
            ],
            [
                'tag'=> 'p',
                'content' => [
                    'Redis 版本：' . $this->container->make('redis')->connection()->getProfile()->getVersion(),
                ],
            ],
        ];
    }
}
