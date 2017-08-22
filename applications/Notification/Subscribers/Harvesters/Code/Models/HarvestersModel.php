<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Notification\Subscribers\Harvesters\Code\Models;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\Model\BaseModel;
use Kazist\KazistFactory;
use Kazist\Service\Email\Email;
use Kazist\Service\System\System;
use Kazist\Service\Database\Query;

/**
 * Description of MarketplaceModel
 *
 * @author sbc
 */
class HarvestersModel extends BaseModel {

//put your code here

    public function emailHarverster() {

        $harvesters = $this->getHarverters();

        if (!empty($harvesters)) {
            foreach ($harvesters as $harvester) {
                $group_id = $this->registerSubscriberGroup($harvester);
                $this->processHarvesterEmails($harvester, $group_id);
            }
        }
    }

    public function processHarvesterEmails($harvester, $group_id) {

        $factory = new KazistFactory();

        $table_name = strtolower(str_replace('/', '_', $harvester->extension_path));

        $records = $factory->getRecords($table_name, 'tx');

        if (!empty($records)) {
            foreach ($records as $record) {
                $this->saveEmails($record, $group_id, $harvester->user_field, $harvester->email_field);
            }
        }
    }

    public function saveEmails($record, $group_id, $user_field, $email_field) {

        $factory = new KazistFactory();

        if ($email_field <> '') {
            $email = $record->$email_field;
        }

        if ($email == '') {
            $user_id = ($user_field <> '' && isset($record->$user_field)) ? $record->$user_field : $record->created_by;

            $user = $factory->getRecord('#__users_users', 'uu', array('id=:id'), array('id' => $user_id));
          
            $email = $user->email;
        }

        if ((!filter_var($email, FILTER_VALIDATE_EMAIL) === false)) {

            $subscriber = $factory->getRecord('#__notification_subscribers', 'ns', array('email=:email'), array('email' => $email));

            if (!is_object($subscriber)) {
                $data_obj = new \stdClass();
                $data_obj->email = $email;
                $subscriber_id = $factory->saveRecord('#__notification_subscribers', $data_obj, array('email=:email'), $data_obj);
            } else {
                $subscriber_id = $subscriber->id;
            }

            $new_data_obj = new \stdClass();
            $new_data_obj->subscriber_id = $subscriber_id;
            $new_data_obj->group_id = $group_id;


            $where_arr = array();
            $where_arr[] = 'subscriber_id=:subscriber_id';
            $where_arr[] = 'group_id=:group_id';

            $factory->saveRecord('#__notification_groups_subscribers', $new_data_obj, $where_arr, $new_data_obj);
        }
    }

    public function registerSubscriberGroup($harvester) {

        $factory = new KazistFactory();


        $query = new Query();
        $query->select('ng.*');
        $query->from('#__notification_groups', 'ng');
        $query->where('ng.id=:id');
        $query->setParameter('id', $harvester->group_id);
        $group = $query->loadObject();


        if (!is_object($group)) {

            $data_obj = new \stdClass();
            $data_obj->title = $harvester->title;
            $data_obj->name = ($harvester->name <> '') ? $harvester->name : $harvester->title;
            $data_obj->description = ($harvester->description <> '') ? $harvester->description : $harvester->title;
            $group_id = $factory->saveRecord('#__notification_groups', $data_obj);

            $harvester_data_obj = new \stdClass();
            $harvester_data_obj->id = $harvester->id;
            $harvester_data_obj->group_id = $group_id;
            $factory->saveRecord('#__notification_subscribers_harvesters', $harvester_data_obj);

            return $group_id;
        } else {
            return $group->id;
        }
    }

    public function getHarverters() {

        $factory = new KazistFactory();
        //TODO
        $query = new Query();
        $query->select('nsh.*');
        $query->from('#__notification_subscribers_harvesters', 'nsh');
        $query->where('1=1');
        $query->orWhere('((nsh.email_field = \'\' OR  nsh.email_field IS NULL) AND nsh.user_field <> \'\')');
        $query->orWhere('((nsh.user_field = \'\' OR nsh.user_field IS NULL) AND nsh.email_field <> \'\')');
        $query->orWhere('(nsh.user_field <> \'\' AND nsh.email_field <> \'\')');

        $records = $query->loadObjectList();

        return $records;
    }

}
