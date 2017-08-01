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
use Illuminate\Support\Collection;
use Notadd\Foundation\Extension\ExtensionManager;
use Notadd\Foundation\Module\Module;
use Notadd\Foundation\Module\ModuleManager;
use Notadd\Foundation\Routing\Abstracts\Handler;

/**
 * Class InfoHandler.
 */
class InfoHandler extends Handler
{
    /**
     * @var \Notadd\Foundation\Extension\ExtensionManager
     */
    private $extension;

    /**
     * @var \Notadd\Foundation\Module\ModuleManager
     */
    private $module;

    /**
     * InfoHandler constructor.
     *
     * @param \Illuminate\Container\Container               $container
     * @param \Notadd\Foundation\Extension\ExtensionManager $extension
     * @param \Notadd\Foundation\Module\ModuleManager       $module
     */
    public function __construct(Container $container, ExtensionManager $extension, ModuleManager $module)
    {
        parent::__construct($container);
        $this->extension = $extension;
        $this->module = $module;
    }

    /**
     * Execute Handler.
     *
     * @throws \Exception
     */
    protected function execute()
    {
        $scripts = new Collection();
        $stylesheets = new Collection();
        $this->module->getEnabledModules()->each(function (Module $module) use ($scripts, $stylesheets) {
            foreach ((array)$module->scripts('administration') as $entry => $script) {
                $scripts->put($entry, $script);
            }
            foreach ((array)$module->stylesheets('administration') as $entry => $stylesheet) {
                $stylesheets->put($entry, $stylesheet);
            }
        });
        $this->withCode(200)->withData([
            'scripts'     => $scripts->toArray(),
            'stylesheets' => $stylesheets->toArray(),
        ])->withMessage('获取模块和插件信息成功！');

    }
}
