<?php
/**
 * This file is part of Notadd.
 *
 * @author        TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime      2017-08-26 10:09
 */
namespace Notadd\Administration\Handlers;

use Illuminate\Container\Container;
use Notadd\Foundation\Module\Module;
use Notadd\Foundation\Module\ModuleManager;
use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Setting\Contracts\SettingsRepository;

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
     * @var \Notadd\Foundation\Setting\Contracts\SettingsRepository
     */
    protected $setting;

    /**
     * DashboardHandler constructor.
     *
     * @param \Illuminate\Container\Container                         $container
     * @param \Notadd\Foundation\Module\ModuleManager                 $module
     * @param \Notadd\Foundation\Setting\Contracts\SettingsRepository $setting
     */
    public function __construct(Container $container, ModuleManager $module, SettingsRepository $setting)
    {
        parent::__construct($container);
        $this->module = $module;
        $this->setting = $setting;
    }

    /**
     * Execute Handler.
     *
     * @throws \Exception
     */
    protected function execute()
    {
        $dashboards = collect();
        $hidden = collect();
        $left = collect();
        $right = collect();
        $this->module->getEnabledModules()->each(function (Module $module) use ($dashboards) {
            $module->offsetExists('dashboards') && collect($module->get('dashboards'))->each(function (
                $definition,
                $identification
            ) use ($dashboards) {
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
                $dashboards->put($identification, $definition);
            });
        });
        $dashboards = $dashboards->keyBy('identification');
        $saved = collect(json_decode($this->setting->get('administration.dashboards', '')));
        $saved->has('hidden') && collect($saved->get('hidden', []))->each(function ($identification) use ($dashboards, $hidden) {
            if ($dashboards->has($identification)) {
                $hidden->push($dashboards->get($identification));
                $dashboards->offsetUnset($identification);
            }
        });
        $saved->has('left') && collect($saved->get('left', []))->each(function ($identification) use ($dashboards, $left) {
            if ($dashboards->has($identification)) {
                $left->push($dashboards->get($identification));
                $dashboards->offsetUnset($identification);
            }
        });
        $saved->has('right') && collect($saved->get('right', []))->each(function ($identification) use ($dashboards, $right) {
            if ($dashboards->has($identification)) {
                $right->push($dashboards->get($identification));
                $dashboards->offsetUnset($identification);
            }
        });
        if ($dashboards->isNotEmpty()) {
            $dashboards->each(function ($definition) use ($left) {
                $left->push($definition);
            });
        }
        $this->withCode(200)->withData([
            'hidden' => $hidden->toArray(),
            'left'   => $left->toArray(),
            'right'  => $right->toArray(),
        ])->withMessage('获取面板数据成功！');
    }
}
