<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-06-13 21:09
 */
namespace Notadd\Administration\Events;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Class Logined.
 */
class Logined
{
    /**
     * @var \Illuminate\Contracts\Auth\Authenticatable
     */
    protected $authenticatable;

    /**
     * Logined constructor.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $authenticatable
     *
     * @internal param \Illuminate\Container\Container $container
     */
    public function __construct(Authenticatable $authenticatable)
    {
        $this->authenticatable = $authenticatable;
    }

    /**
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function getAuthenticatable(): \Illuminate\Contracts\Auth\Authenticatable
    {
        return $this->authenticatable;
    }
}
