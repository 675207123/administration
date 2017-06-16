<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-06-14 12:04
 */
namespace Notadd\Administration\Entities;

use Notadd\Foundation\Database\Model;
use Notadd\Foundation\Flow\Abstracts\Entity;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Transition;

/**
 * Class Administration.
 */
class Administration extends Entity
{
    /**
     * @var \Illuminate\Contracts\Auth\Authenticatable
     */
    protected $authenticatable;

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
        $authenticatable = $event->getSubject()->getAuthenticatable();
        switch ($event->getTransition()->getName()) {
            case 'login':
                if ($authenticatable && $authenticatable instanceof Model) {
                    if ($authenticatable->hasExtendRelation('groups')) {
                        $groups = $authenticatable->load('groups')->getAttribute('groups');
                        $this->block($event, $this->permission('global::administration::global::entry', $groups));
                    } else {
                        $this->block($event, true);
                    }
                } else {
                    $this->block($event, false);
                }
                break;
            case 'need_to_logout':
                $this->block($event, $this->permission('', 0));
                break;
            case 'logout':
                $this->block($event, $this->permission('', 0));
                break;
            default:
                $event->setBlocked(true);
        }
    }

    /**
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function getAuthenticatable()
    {
        return $this->authenticatable;
    }

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $authenticatable
     */
    public function setAuthenticatable($authenticatable)
    {
        $this->authenticatable = $authenticatable;
    }
}
