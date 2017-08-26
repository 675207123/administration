<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-08-26 10:09
 */
namespace Notadd\Administration\Handlers;

use Illuminate\Container\Container;
use Notadd\Foundation\Module\Module;
use Notadd\Foundation\Module\ModuleManager;
use Notadd\Foundation\Routing\Abstracts\Handler;

/**
 * Class DashboardHandler.
 */
class DashboardHandler extends Handler
{
    /**
     * @var \Notadd\Foundation\Module\ModuleManager
     */
    protected $module;

    /**
     * DashboardHandler constructor.
     *
     * @param \Illuminate\Container\Container         $container
     * @param \Notadd\Foundation\Module\ModuleManager $module
     */
    public function __construct(Container $container, ModuleManager $module)
    {
        parent::__construct($container);
        $this->module = $module;
    }

    /**
     * Execute Handler.
     *
     * @throws \Exception
     */
    protected function execute()
    {
        $data = collect();
        $this->module->getEnabledModules()->each(function (Module $module) use ($data) {
            $module->offsetExists('dashboards') && collect($module->get('dashboards'))->each(function ($definition) use ($data) {
                if (is_string($definition['template'])) {
                    list($class, $method) = explode('@', $definition['template']);
                    if (class_exists($class)) {
                        $instance = $this->container->make($class);
                        $definition['template'] = $this->container->call([
                            $instance,
                            $method,
                        ]);
                    }
                }
                $data->push($definition);
            });
        });
        $this->withCode(200)->withData($data)->withMessage('获取面板数据成功！');
    }
}
