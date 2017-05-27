<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-27 18:10
 */
namespace Notadd\Administration\Listeners;

use Notadd\Foundation\Permission\Abstracts\PermissionModuleRegister as AbstractPermissionModuleRegister;

/**
 * Class PermissionModuleRegister.
 */
class PermissionModuleRegister extends AbstractPermissionModuleRegister
{
    /**
     * Handle Permission Register.
     */
    public function handle()
    {
        $this->manager->extend([
            'description'    => '后台权限。',
            'identification' => 'administration',
            'name'           => '后台',
        ]);
    }
}