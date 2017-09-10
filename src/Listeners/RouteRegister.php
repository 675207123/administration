<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-02-18 14:12
 */
namespace Notadd\Administration\Listeners;

use Notadd\Administration\Controllers\AdminController;
use Notadd\Administration\Controllers\InjectionController;
use Notadd\Foundation\Routing\Abstracts\RouteRegister as AbstractRouteRegister;

/**
 * Class RouteRegister.
 */
class RouteRegister extends AbstractRouteRegister
{
    /**
     * Handle Route Register.
     */
    public function handle()
    {
        $this->router->group(['middleware' => ['cross', 'web'], 'prefix' => 'api/administration'], function () {
            $this->router->post('token', InjectionController::class . '@token');
        });
        $this->router->group(['middleware' => ['auth:api', 'cross', 'web'], 'prefix' => 'api/administration'], function () {
            $this->router->post('access', InjectionController::class . '@access');
            $this->router->post('configuration', InjectionController::class . '@configuration');
            $this->router->post('dashboard', InjectionController::class . '@dashboard');
            $this->router->post('extension', InjectionController::class . '@extension');
            $this->router->post('info', InjectionController::class . '@info');
            $this->router->post('menu', InjectionController::class . '@menu');
            $this->router->post('module', InjectionController::class . '@module');
            $this->router->post('save', InjectionController::class . '@save');
        });
    }
}
