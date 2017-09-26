<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-03-10 14:12
 */
namespace Notadd\Administration\Controllers;

use Notadd\Administration\Handlers\AccessHandler;
use Notadd\Administration\Handlers\ConfigurationHandler;
use Notadd\Administration\Handlers\DashboardHandler;
use Notadd\Administration\Handlers\InfoHandler;
use Notadd\Administration\Handlers\SaveHandler;
use Notadd\Administration\Handlers\TokenHandler;
use Notadd\Foundation\Routing\Abstracts\Controller;

/**
 * Class InjectionController.
 */
class InjectionController extends Controller
{
    /**
     * @param \Notadd\Administration\Handlers\AccessHandler $handler
     *
     * @return \Notadd\Foundation\Passport\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     */
    public function access(AccessHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Administration\Handlers\ConfigurationHandler $handler
     *
     * @return \Notadd\Foundation\Passport\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     */
    public function configuration(ConfigurationHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Administration\Handlers\DashboardHandler $handler
     *
     * @return \Notadd\Foundation\Passport\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     */
    public function dashboard(DashboardHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Administration\Handlers\InfoHandler $handler
     *
     * @return \Notadd\Foundation\Passport\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     */
    public function info(InfoHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Administration\Handlers\TokenHandler $handler
     *
     * @return \Notadd\Foundation\Passport\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     */
    public function token(TokenHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Administration\Handlers\SaveHandler $handler
     *
     * @return \Notadd\Foundation\Passport\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     */
    public function save(SaveHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
}
