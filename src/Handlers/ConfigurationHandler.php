<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-08-17 22:15
 */
namespace Notadd\Administration\Handlers;

use Illuminate\Container\Container;
use Notadd\Foundation\Extension\Extension;
use Notadd\Foundation\Extension\ExtensionManager;
use Notadd\Foundation\Module\Module;
use Notadd\Foundation\Module\ModuleManager;
use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;

/**
 * Class ConfigurationHandler.
 */
class ConfigurationHandler extends Handler
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
     * ConfigurationHandler constructor.
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
        $this->validate($this->request, [
            'page' => Rule::required(),
            'type' => [
                Rule::in([
                    'extension',
                    'module',
                ]),
                Rule::required(),
            ],
        ], [
            'page.required' => '页面参数必须填写',
            'type.in'       => '类型参数错误',
            'type.required' => '类型参数必须填写',
        ]);
        $pages = collect();
        switch ($this->request->input('type')) {
            case 'extension':
                $this->extension->getEnabledExtensions()->map(function (Extension $extension) use ($pages) {
                    collect((array)$extension->get('pages', []))->map(function ($definition, $identification) {

                    });
                });
                break;
            case 'module':
                $this->module->getEnabledModules()->map(function (Module $module) use ($pages) {
                    collect((array)$module->get('pages', []))->map(function ($definition, $identification) {

                    });
                });
                break;
        }
    }
}
