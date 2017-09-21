<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-09-10 15:55
 */
namespace Notadd\Administration\Handlers;

use Illuminate\Container\Container;
use Notadd\Foundation\Module\Module;
use Notadd\Foundation\Module\ModuleManager;
use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Setting\Contracts\SettingsRepository;

/**
 * Class MenuHandler.
 */
class MenuHandler extends Handler
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
     * MenuHandler constructor.
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
        $configurations = collect((array)json_decode($this->setting->get('administration.menus', ''), true));
        $menus = collect();
        $this->module->repository()->enabled()->map(function (Module $module) use ($configurations, $menus) {
            collect((array)$module->get('menus', []))->each(function ($definition, $identification) use ($configurations, $menus) {
                $configuration = $configurations->get($identification);
                $definition['identification'] = $identification;
                if (is_array($configuration)) {
                    $definition['order'] = isset($configuration['order']) ? intval($configuration['order']) : 0;
                    $definition['show'] = isset($configuration['show']) ? boolval($configuration['show']) : true;
                } else {
                    $definition['order'] = 0;
                    $definition['show'] = true;
                }
                $menus->put($identification, $definition);
            });
        });
        $this->withCode(200)->withData($menus->sortBy('order'))->withMessage('获取菜单数据成功！');
    }
}
