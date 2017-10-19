<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2016, notadd.com
 * @datetime 2016-11-08 13:51
 */
namespace Notadd\Administration\Controllers;

use Notadd\Foundation\Routing\Abstracts\Controller;

/**
 * Class AdminController.
 */
class AdminController extends Controller
{
    /**
     * Return index content.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function handle()
    {
        $this->share('translations', json_encode($this->translator->fetch('zh-cn')));

        return $this->view('admin::layout');
    }
}
