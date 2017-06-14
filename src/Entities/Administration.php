<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-06-14 12:04
 */
namespace Notadd\Administration\Entities;

use Notadd\Foundation\Flow\Abstracts\Entity;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Transition;

/**
 * Class Administration.
 */
class Administration extends Entity
{
    /**
     * Definition of name for flow.
     *
     * @return string
     */
    public function name()
    {
        return 'administration';
    }

    /**
     * Definition of places for flow.
     *
     * @return array
     */
    public function places()
    {
        return [
            'login',
            'logined',
            'logout',
            'loggedout',
        ];
    }

    /**
     * Definition of transitions for flow.
     *
     * @return array
     */
    public function transitions()
    {
        return [
            new Transition('login', 'login', 'logined'),
            new Transition('need_to_logout', 'logined', 'logout'),
            new Transition('logout', 'logout', 'loggedout'),
        ];
    }

    /**
     * Guard a transition.
     *
     * @param \Symfony\Component\Workflow\Event\GuardEvent $event
     */
    public function guard(GuardEvent $event)
    {
        switch ($event->getTransition()->getName()) {
            case 'login':
                $this->block($event, $this->permission(''));
                break;
            case 'need_to_logout':
                $this->block($event, $this->permission(''));
                break;
            case 'logout':
                $this->block($event, $this->permission(''));
                break;
            default:
                $event->setBlocked(true);
        }
    }
}
