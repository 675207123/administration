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
use Notadd\Foundation\Extension\Extension;
use Notadd\Foundation\Extension\ExtensionManager;
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
     * @var \Notadd\Foundation\Extension\ExtensionManager
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
     * @param \Notadd\Foundation\Extension\ExtensionManager           $extension
     * @param \Notadd\Foundation\Module\ModuleManager                 $module
     * @param \Notadd\Foundation\Setting\Contracts\SettingsRepository $setting
     */
    public function __construct(Container $container, ExtensionManager $extension, ModuleManager $module, SettingsRepository $setting)
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
        $pages = collect();
        $scripts = collect();
        $stylesheets = collect();
        $this->extension->getEnabledExtensions()->each(function (Extension $extension) use ($pages, $scripts, $stylesheets) {
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
        $this->module->getEnabledModules()->each(function (Module $module) use ($pages, $scripts, $stylesheets) {
            collect((array)$module->get('pages', []))->map(function ($definition, $identification) {
                $definition['initialization']['identification'] = $identification;
                unset($definition['initialization']['tabs']);
                return $definition['initialization'];
            })->groupBy('target')->each(function ($data, $target) use ($pages) {
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
            'debug'       => boolval($this->setting->get('debug.enabled', false)),
            'pages'       => $pages->toArray(),
            'scripts'     => $scripts->toArray(),
            'stylesheets' => $stylesheets->toArray(),
        ])->withMessage('获取模块和插件信息成功！');
    }
}
