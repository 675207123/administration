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
        if (method_exists($this->parser, 'getDistro')) {
            $distribution = $this->parser->getDistro();
            if (is_array($distribution) && isset($distribution['name']) && isset($distribution['version'])) {
                $distribution = $distribution['name'] . ' ' . $distribution['version'];
            } else {
                $distribution = $this->parser->getOS();
            }
        } else {
            $distribution = $this->parser->getOS();
        }
        $pdo = $this->db->getPdo();

        return [
            [
                'content' => [
                    [
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
                        'style'   => [
                            'lineHeight' => '30px',
                        ],
                        'tag'     => 'p',
                    ],
                    [
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
                        'style'   => [
                            'lineHeight' => '30px',
                        ],
                        'tag'     => 'p',
                    ],
                    [
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
                        'style'   => [
                            'lineHeight' => '30px',
                        ],
                        'tag'     => 'p',
                    ],
                    [
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
                        'style'   => [
                            'lineHeight' => '30px',
                        ],
                        'tag'     => 'p',
                    ],
                    [
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
                        'style'   => [
                            'lineHeight' => '30px',
                        ],
                        'tag'     => 'p',
                    ],
                    [
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
                        'style'   => [
                            'lineHeight' => '30px',
                        ],
                        'tag'     => 'p',
                    ],
                    [
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
                        'style'   => [
                            'lineHeight' => '30px',
                        ],
                        'tag'     => 'p',
                    ],
                    [
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
                        'style'   => [
                            'lineHeight' => '30px',
                        ],
                        'tag'     => 'p',
                    ],
                    [
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
                        'style'   => [
                            'lineHeight' => '30px',
                        ],
                        'tag'     => 'p',
                    ],
                ],
                'style'   => [
                    'padding' => '24px 0',
                ],
                'tag'     => 'div',
            ],
        ];
    }
}
