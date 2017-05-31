<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-03-30 18:25
 */
namespace Notadd\Administration\Handlers;

use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use Notadd\Foundation\Extension\Extension;
use Notadd\Foundation\Extension\ExtensionManager;
use Notadd\Foundation\Routing\Abstracts\Handler;

class ExtensionHandler extends Handler
{
    /**
     * @var \Notadd\Foundation\Extension\ExtensionManager
     */
    protected $manager;

    /**
     * ExtensionHandler constructor.
     *
     * @param \Illuminate\Container\Container               $container
     * @param \Notadd\Foundation\Extension\ExtensionManager $manager
     */
    public function __construct(Container $container, ExtensionManager $manager)
    {
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
        $all = $this->manager->getExtensions();
        $enabled = $this->manager->getEnabledExtensions();
        $installed = $this->manager->getInstalledExtensions();
        $notInstalled = $this->manager->getNotInstalledExtensions();
        $this->success()->withData([
            'all' => $this->info($all),
            'enabled' => $this->info($enabled),
            'installed' => $this->info($installed),
            'notInstall' => $this->info($notInstalled),
        ])->withMessage('获取插件列表成功！');
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
        $list->each(function (Extension $extension) use($data) {
            $data->put($extension->getIdentification(), [
                'author' => collect($extension->getAuthor())->implode(','),
                'enabled' => $extension->isEnabled(),
                'description' => $extension->getDescription(),
                'identification' => $extension->getIdentification(),
                'name' => $extension->getName(),
            ]);
        });

        return $data->toArray();
    }
}
