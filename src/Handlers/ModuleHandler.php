<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-03-10 16:51
 */
namespace Notadd\Administration\Handlers;

use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use Notadd\Foundation\Module\Module;
use Notadd\Foundation\Module\ModuleManager;
use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Setting\Contracts\SettingsRepository;

/**
 * Class ModuleHandler.
 */
class ModuleHandler extends Handler
{
    /**
     * @var \Notadd\Foundation\Module\ModuleManager
     */
    protected $manager;

    /**
     * @var \Notadd\Foundation\Setting\Contracts\SettingsRepository
     */
    private $setting;

    /**
     * ModuleHandler constructor.
     *
     * @param \Illuminate\Container\Container                         $container
     * @param \Notadd\Foundation\Module\ModuleManager                 $manager
     * @param \Notadd\Foundation\Setting\Contracts\SettingsRepository $setting
     */
    public function __construct(Container $container, ModuleManager $manager, SettingsRepository $setting)
    {
        parent::__construct($container);
        $this->manager = $manager;
        $this->setting = $setting;
    }

    /**
     * Execute Handler.
     *
     * @throws \Exception
     */
    protected function execute()
    {
        $modules = $this->manager->getModules();
        $enabled = $this->manager->getEnabledModules();
        $installed = $this->manager->getInstalledModules();
        $notInstalled = $this->manager->getNotInstalledModules();
        $modules->offsetUnset('notadd/administration');
        $domains = $modules->map(function (Module $module) {
            $data = [];
            $alias = 'module.' . $module->identification() . '.domain.alias';
            $enabled = 'module.' . $module->identification() . '.domain.enabled';
            $host = 'module.' . $module->identification() . '.domain.host';
            $data['identification'] = $module->identification();
            $data['name'] = $module->name();
            $data['alias'] = $this->setting->get($alias, '');
            $data['enabled'] = boolval($this->setting->get($enabled, 0));
            $data['host'] = $this->setting->get($host, '');

            return $data;
        });
        $domains->prepend([
            'alias' => '/',
            'enabled' => boolval($this->setting->get('module.notadd/notadd.domain.enabled', 0)),
            'host' => $this->setting->get('module.notadd/notadd.domain.host', ''),
            'identification' => 'notadd/notadd',
            'name' => 'Notadd',
        ], 'notadd/notadd');
        $enabled->offsetUnset('notadd/administration');
        $installed->offsetUnset('notadd/administration');
        $notInstalled->offsetUnset('notadd/administration');
        $this->withCode(200)->withData([
            'all'        => $this->info($modules),
            'domains'    => $domains->toArray(),
            'enabled'    => $this->info($enabled),
            'installed'  => $this->info($installed),
            'notInstall' => $this->info($notInstalled),
        ])->withMessage('获取模块列表成功！');
    }

    /**
     * Info list.
     *
     * @param \Illuminate\Support\Collection $list
     *
     * @return array
     */
    protected function info(Collection $list)
    {
        $data = new Collection();
        $list->each(function (Module $module) use ($data) {
            $data->put($module->identification(), [
                'author'         => collect($module->author())->implode(','),
                'enabled'        => boolval($module->enabled()),
                'description'    => $module->description(),
                'identification' => $module->identification(),
                'name'           => $module->name(),
            ]);
        });

        return $data->toArray();
    }
}
