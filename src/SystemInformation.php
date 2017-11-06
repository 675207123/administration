<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-08-26 13:55
 */
namespace Notadd\Administration;

use Linfo\Linfo;
use Notadd\Foundation\Routing\Traits\Helpers;
use PDO;

/**
 * Class SystemInformation.
 */
class SystemInformation
{
    use Helpers;

    /**
     * @var \Linfo\OS\OS
     */
    protected $parser;

    /**
     * SystemInformation constructor.
     *
     * @param \Linfo\Linfo $linfo
     */
    public function __construct(Linfo $linfo)
    {
        $this->parser = $linfo->getParser();
    }

    /**
     * @return array
     */
    public function handler()
    {
        $distribution = $this->parser->getDistro();
        if (is_array($distribution) && isset($distribution['name']) && isset($distribution['version'])) {
            $distribution = $distribution['name'] . ' ' . $distribution['version'];
        } else {
            $distribution = $this->parser->getOS();
        }
        $pdo = $this->db->getPdo();
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
                    'PHP 版本：' . $this->parser->getPhpVersion(),
                ],
            ],
            [
                'tag'=> 'p',
                'content' => [
                    '系统版本：' . $distribution,
                ],
            ],
            [
                'tag'=> 'p',
                'content' => [
                    'CPU：' . $this->parser->getCPUArchitecture(),
                ],
            ],
            [
                'tag'=> 'p',
                'content' => [
                    '服务器架构：' . $this->parser->getWebService(),
                ],
            ],
            [
                'tag'=> 'p',
                'content' => [
                    '内存大小：' . $this->parser->getRam()['total'] . ' Bytes',
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
            [
                'tag'=> 'p',
                'content' => [
                    '当前时区：' . date_default_timezone_get(),
                ],
            ],
        ];
    }
}
