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
use Notadd\Foundation\Passport\Abstracts\Handler;

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
     * ModuleHandler constructor.
     *
     * @param \Illuminate\Container\Container         $container
     * @param \Notadd\Foundation\Module\ModuleManager $manager
     */
    public function __construct(Container $container, ModuleManager $manager) {
        parent::__construct($container);
        $this->manager = $manager;
    }

    /**
     * Execute Handler.
     *
     * @throws \Exception
     */
    protected function execute()
    {
        $all = $this->manager->getModules();
        $enabled = $this->manager->getEnabledModules();
        $installed = $this->manager->getInstalledModules();
        $notInstalled = $this->manager->getNotInstalledModules();
        $all->offsetUnset('notadd/administration');
        $enabled->offsetUnset('notadd/administration');
        $installed->offsetUnset('notadd/administration');
        $notInstalled->offsetUnset('notadd/administration');
        $this->success()->withData([
            'all' => $this->info($all),
            'enabled' => $this->info($enabled),
            'installed' => $this->info($installed),
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
    protected function info(Collection $list) {
        $data = new Collection();
        $list->each(function (Module $module) use($data) {
            $data->put($module->getIdentification(), [
                'author' => collect($module->getAuthor())->implode(','),
                'enabled' => $module->isEnabled(),
                'description' => $module->getDescription(),
                'identification' => $module->getIdentification(),
                'name' => $module->getName(),
            ]);
        });

        return $data->toArray();
    }
}
