<?php

/*
 * This file is part of Kazist Framework.
 * (c) Dedan Irungu <irungudedan@gmail.com>
 *  For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 * 
 */

namespace Users\Users\Code\Listeners;

defined('KAZIST') or exit('Not Kazist Framework');

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Kazist\Event\CRUDEvent;
use Kazist\KazistFactory;

/**
 * Some of Events to be fired
 * - user.before.registration
 * - user.after.registration
 * - user.before.login
 * - user.after.login
 * - user.before.save
 * - user.after.save
 * - user.before.delete
 * - user.after.delete
 * 
 */
class UserListener implements EventSubscriberInterface {

    public $container = '';

    public function onUserBeforeSave(CRUDEvent $event) {
        global $sc;

        $this->container = $sc;
    }

    public function onUserAferSave(CRUDEvent $event) {
        global $sc;

        $this->container = $sc;
    }

    public function onUserBeforeDelete(CRUDEvent $event) {
        global $sc;

        $this->container = $sc;
    }

    public function onUserAferDelete(CRUDEvent $event) {
        global $sc;

        $this->container = $sc;

        $factory = new KazistFactory();
        $user = $event->getRecord();

        $data_obj = new \stdClass();
        $data_obj->user_id = $user->getId();

        $factory->deleteRecords('#__users_users_groups', array('user_id=:user_id'), $data_obj);
        $factory->deleteRecords('#__users_users_roles', array('user_id=:user_id'), $data_obj);
    }

    public static function getSubscribedEvents() {
        return array(
            'users.users.before.save' => 'onUserBeforeSave',
            'users.users.after.save' => 'onUserAferSave',
            'users.users.before.delete' => 'onUserBeforeDelete',
            'users.users.after.delete' => 'onUserAferDelete',
        );
    }

}
