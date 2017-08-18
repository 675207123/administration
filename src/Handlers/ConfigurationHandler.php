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
use Illuminate\Routing\UrlGenerator;
use Notadd\Foundation\Extension\Extension;
use Notadd\Foundation\Extension\ExtensionManager;
use Notadd\Foundation\Module\Module;
use Notadd\Foundation\Module\ModuleManager;
use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Setting\Contracts\SettingsRepository;
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
     * @var \Notadd\Foundation\Setting\Contracts\SettingsRepository
     */
    protected $setting;

    /**
     * ConfigurationHandler constructor.
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
                    collect((array)$module->get('pages', []))->map(function ($definition) {
                        return collect($definition)->map(function ($data, $key) {
                            if ($key == 'tabs') {
                                return collect($data)->map(function ($data) {
                                    if (isset($data['submit'])) {
                                        $data['submit'] = $this->container->make(UrlGenerator::class)->to($data['submit']);
                                    }
                                    return collect($data)->map(function ($data, $key) {
                                        if ($key == 'fields') {
                                            return collect($data)->map(function ($data) {
                                                $setting = $this->setting->get($data['key']);
                                                if (isset($data['format'])) {
                                                    switch ($data['format']) {
                                                        case 'boolean':
                                                            $data['value'] = boolval($setting);
                                                            break;
                                                        default:
                                                            $data['value'] = $setting;
                                                            break;
                                                    }
                                                } else {
                                                    $data['value'] = $setting;
                                                }

                                                return $data;
                                            });
                                        } else {
                                            return $data;
                                        }
                                    });
                                });
                            } else {
                                return $data;
                            }
                        });
                    })->map(function ($definition, $identification) use ($pages) {
                        $pages->put($identification, $definition);
                    });
                });
                break;
        }
        if ($pages->has($this->request->input('page'))) {
            $this->withCode(200)->withData($pages->get($this->request->input('page')))->withMessage('获取页面配置成功！');
        } else {
            $this->withCode(422)->withMessage('没有对应的页面配置信息！');
        }
    }
}
