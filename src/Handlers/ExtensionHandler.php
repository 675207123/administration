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

/**
 * Class ExtensionHandler.
 */
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
        $enabled = $this->manager->getEnabledExtensions();
        $extensions = $this->manager->getExtensions();
        $installed = $this->manager->getInstalledExtensions();
        $notInstalled = $this->manager->getNotInstalledExtensions();
        $this->withCode(200)->withData([
            'enabled'    => $this->info($enabled),
            'extensions' => $this->info($extensions),
            'installed'  => $this->info($installed),
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
    protected function info(Collection $list)
    {
        $data = collect();
        $list->each(function (Extension $extension) use ($data) {
            $data->put($extension->identification(), [
                'author'         => collect($extension->offsetGet('author'))->implode(','),
                'enabled'        => $extension->isEnabled(),
                'description'    => $extension->offsetGet('description'),
                'identification' => $extension->identification(),
                'name'           => $extension->offsetGet('name'),
            ]);
        });

        return $data->toArray();
    }
}
