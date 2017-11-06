<?php
/**
 * This file is part of Notadd.
 *
 * @author        TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime      2017-08-26 13:55
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
                'tag'     => 'p',
                'content' => [
                    [
                        'content' => [
                            'Notadd 版本：',
                        ],
                        'style'   => [
                            'display'   => 'block',
                            'float'     => 'left',
                            'width'     => '200px',
                            'textAlign' => 'right',
                        ],
                        'tag'     => 'strong',
                    ],
                    $this->container->version(),
                ],
            ],
            [
                'tag'     => 'p',
                'content' => [
                    [
                        'content' => [
                            'PHP 版本：',
                        ],
                        'style'   => [
                            'display'   => 'block',
                            'float'     => 'left',
                            'width'     => '200px',
                            'textAlign' => 'right',
                        ],
                        'tag'     => 'strong',
                    ],
                    $this->parser->getPhpVersion(),
                ],
            ],
            [
                'tag'     => 'p',
                'content' => [
                    [
                        'content' => [
                            '系统版本：',
                        ],
                        'style'   => [
                            'display'   => 'block',
                            'float'     => 'left',
                            'width'     => '200px',
                            'textAlign' => 'right',
                        ],
                        'tag'     => 'strong',
                    ],
                    $distribution,
                ],
            ],
            [
                'tag'     => 'p',
                'content' => [
                    [
                        'content' => [
                            'CPU：',
                        ],
                        'style'   => [
                            'display'   => 'block',
                            'float'     => 'left',
                            'width'     => '200px',
                            'textAlign' => 'right',
                        ],
                        'tag'     => 'strong',
                    ],
                    $this->parser->getCPUArchitecture(),
                ],
            ],
            [
                'tag'     => 'p',
                'content' => [
                    [
                        'content' => [
                            '服务器架构：',
                        ],
                        'style'   => [
                            'display'   => 'block',
                            'float'     => 'left',
                            'width'     => '200px',
                            'textAlign' => 'right',
                        ],
                        'tag'     => 'strong',
                    ],
                    $this->parser->getWebService(),
                ],
            ],
            [
                'tag'     => 'p',
                'content' => [
                    [
                        'content' => [
                            '内存大小：',
                        ],
                        'style'   => [
                            'display'   => 'block',
                            'float'     => 'left',
                            'width'     => '200px',
                            'textAlign' => 'right',
                        ],
                        'tag'     => 'strong',
                    ],
                    $this->parser->getRam()['total'] . ' Bytes',
                ],
            ],
            [
                'tag'     => 'p',
                'content' => [
                    [
                        'content' => [
                            '数据库版本：',
                        ],
                        'style'   => [
                            'display'   => 'block',
                            'float'     => 'left',
                            'width'     => '200px',
                            'textAlign' => 'right',
                        ],
                        'tag'     => 'strong',
                    ],
                    $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) . ' ' . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION),
                ],
            ],
            [
                'tag'     => 'p',
                'content' => [
                    [
                        'content' => [
                            'Redis 版本：',
                        ],
                        'style'   => [
                            'display'   => 'block',
                            'float'     => 'left',
                            'width'     => '200px',
                            'textAlign' => 'right',
                        ],
                        'tag'     => 'strong',
                    ],
                    $this->container->make('redis')->connection()->getProfile()->getVersion(),
                ],
            ],
            [
                'tag'     => 'p',
                'content' => [
                    [
                        'content' => [
                            '当前时区：',
                        ],
                        'style'   => [
                            'display'   => 'block',
                            'float'     => 'left',
                            'width'     => '200px',
                            'textAlign' => 'right',
                        ],
                        'tag'     => 'strong',
                    ],
                    date_default_timezone_get(),
                ],
            ],
        ];
    }
}
