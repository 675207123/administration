<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-09-11 15:06
 */
namespace Notadd\Administration\Handlers;

use Illuminate\Container\Container;
use Notadd\Foundation\Module\ModuleManager;
use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Setting\Contracts\SettingsRepository;
use Notadd\Foundation\Validation\Rule;

/**
 * Class NavigationHandler.
 */
class NavigationHandler extends Handler
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
        $data = $this->validate($this->request, [
            'data' => [
                Rule::array(),
                Rule::required(),
            ],
        ], [
            'data.array'    => '数据必须为数组',
            'data.required' => '数据必须填写',
        ]);
        $configurations = collect();
        collect($data['data'])->each(function ($definition) use ($configurations) {
            $configurations->put($definition['identification'], [
                'order' => intval($definition['order']),
                'show'  => boolval($definition['show']),
            ]);
        });
        $this->setting->set('administration.menus', json_encode($configurations->toArray()));
        $this->withCode(200)->withMessage('更新菜单数据成功！');
    }
}
