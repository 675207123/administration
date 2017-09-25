<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-08-01 15:51
 */
namespace Notadd\Administration\Handlers;

use Illuminate\Container\Container;
use Notadd\Foundation\Addon\Addon;
use Notadd\Foundation\Addon\AddonManager;
use Notadd\Foundation\Module\Module;
use Notadd\Foundation\Module\ModuleManager;
use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Setting\Contracts\SettingsRepository;

/**
 * Class InfoHandler.
 */
class InfoHandler extends Handler
{
    /**
     * @var \Notadd\Foundation\Addon\AddonManager
     */
    protected $extension;

    /**
     * @var \Notadd\Foundation\Module\ModuleManager
     */
    protected $module;

    /**
     * @var \Notadd\Foundation\Setting\Contracts\SettingsRepository
     */
    protected $setting;

    /**
     * InfoHandler constructor.
     *
     * @param \Illuminate\Container\Container                         $container
     * @param \Notadd\Foundation\Addon\AddonManager                   $extension
     * @param \Notadd\Foundation\Module\ModuleManager                 $module
     * @param \Notadd\Foundation\Setting\Contracts\SettingsRepository $setting
     */
    public function __construct(Container $container, AddonManager $extension, ModuleManager $module, SettingsRepository $setting)
    {
        parent::__construct($container);
        $this->extension = $extension;
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
        $navigation = collect();
        $pages = collect();
        $scripts = collect();
        $stylesheets = collect();
        // Get data from extensions.
        $this->extension->repository()->enabled()->each(function (Addon $extension) use ($pages, $scripts, $stylesheets) {
            collect((array)$extension->get('pages', []))->map(function ($definition, $identification) {
                $definition['initialization']['identification'] = $identification;
                unset($definition['initialization']['tabs']);

                return $definition['initialization'];
            })->groupBy('target')->each(function ($data, $target) use ($pages) {
                if ($pages->has($target)) {
                    $data = array_merge($pages->get($target), $data);
                }
                $pages->put($target, $data);
            });
            foreach ((array)$extension->scripts() as $entry => $script) {
                $scripts->put($entry, [
                    'link' => $script,
                    'name' => $extension->offsetGet('name'),
                    'type' => 'extension',
                ]);
            }
            foreach ((array)$extension->stylesheets() as $entry => $stylesheet) {
                $stylesheets->put($entry, $stylesheet);
            }
        });
        // Get data from modules.
        $this->module->repository()->enabled()->each(function (Module $module) use ($configurations, $navigation, $pages, $scripts, $stylesheets) {
            collect((array)$module->get('menus', []))->each(function ($definition, $identification) use ($configurations, $navigation) {
                $configuration = $configurations->get($identification);
                $definition['identification'] = $identification;
                if (is_array($configuration)) {
                    $definition['order'] = isset($configuration['order']) ? intval($configuration['order']) : 0;
                    $definition['show'] = isset($configuration['show']) ? boolval($configuration['show']) : true;
                } else {
                    $definition['order'] = 0;
                    $definition['show'] = true;
                }
                $definition['show'] && $navigation->put($identification, $definition);
            });
            $module->pages()->each(function ($data, $target) use ($pages) {
                if ($pages->has($target)) {
                    $data = array_merge($pages->get($target), $data);
                }
                $pages->put($target, $data);
            });
            foreach ((array)$module->scripts('administration') as $entry => $script) {
                $scripts->put($entry, [
                    'link' => $script,
                    'name' => $module->offsetGet('name'),
                    'type' => 'module',
                ]);
            }
            foreach ((array)$module->stylesheets('administration') as $entry => $stylesheet) {
                $stylesheets->put($entry, $stylesheet);
            }
        });
        $this->withCode(200)->withData([
            'navigation'  => $navigation->sortBy('order')->values()->toArray(),
            'pages'       => $pages->toArray(),
            'scripts'     => $scripts->toArray(),
            'stylesheets' => $stylesheets->toArray(),
        ])->withMessage('获取模块和插件信息成功！');
    }
}
