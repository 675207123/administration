<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2016, notadd.com
 * @datetime 2016-10-25 17:42
 */
namespace Notadd\Administration;

use Illuminate\Contracts\Foundation\Application;
use Notadd\Administration\Controllers\AdminController;
use Notadd\Foundation\Administration\Administration;
use Notadd\Foundation\Module\Abstracts\Module;

/**
 * Class Extension.
 */
class ModuleServiceProvider extends Module
{
    /**
     * Boot service provider.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot()
    {
        $administrator = new Administrator($this->app['events'], $this->app['router']);
        $administrator->registerPath('admin');
        $administrator->registerHandler(AdminController::class . '@handle');
        $this->app->make(Administration::class)->setAdministrator($administrator);
        $this->loadTranslationsFrom(realpath(__DIR__ . '/../resources/translations'), 'administration');
        $this->loadViewsFrom(realpath(__DIR__ . '/../resources/views'), 'admin');
    }

    /**
     * Install module.
     *
     * @return bool
     */
    public static function install()
    {
        return true;
    }

    /**
     * Uninstall module.
     *
     * @return mixed
     */
    public static function uninstall()
    {
        return true;
    }
}
