<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Notification\Newsletters\Scheduled\Code\Models;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\Model\BaseModel;
use Kazist\KazistFactory;
use Kazist\Service\Email\Email;
use Kazist\Service\System\System;
use Kazist\Service\Database\Query;
use Kazist\Service\Json\Json;

/**
 * Description of ScheduledModel
 *
 * @author sbc
 */
class ScheduledModel extends BaseModel {

    //put your code here
    public $table_name = '';

    public function sendScheduledList() {

        $sheduledlist = $this->getScheduledList();

        if (!empty($sheduledlist)) {
            foreach ($sheduledlist as $sheduled) {
                $this->sendScheduled($sheduled);
            }
        }
    }

    public function sendScheduled($sheduled) {

        $email = new Email();

        $subject = $sheduled->subject;
        $body = $sheduled->body;
        $query_str = $this->getItemsQuery($sheduled);
        
        $email->priority = 7;
        $email->sendPreparedEmail($subject, $body, '', base64_encode($query_str));

        $data_obj = new \stdClass();
        $data_obj->id = $sheduled->id;
        $data_obj->scheduled_newsletter_date = date('Y-m-d', strtotime('+1 days'));

        $this->saveRecordByEntity('#__notification_newsletters_scheduled', $data_obj);
    }

    function getItemsQuery($sheduled) {

        $json = new Json();
        $system = new System();
        $query_obj = new Query();
        $factory = new KazistFactory();

        $table_name = str_replace('/', '_', $sheduled->extension_path);
        $email_field = $sheduled->email_field;
        $user_field = ($sheduled->user_field <> '') ? $sheduled->user_field : 'created_by';
        $date_field = ($sheduled->date_field <> '') ? $sheduled->date_field : 'date_created';
        $list_limit = ($sheduled->list_limit <> '') ? $sheduled->list_limit : 10;
        $table_alias = $this->getTableAlias($table_name);

        $query = $this->getQueryBuilder($table_name, $table_alias);
        $this->getWhereQuery($query, $sheduled, $table_alias, $date_field);
        $query->setMaxResults($list_limit);


        if ($email_field <> '') {
            $query->addSelect($table_alias . '.' . $email_field . ' AS recipient');
        } else {
            $query->addSelect('uu_scheduled.email AS recipient');
            $query->leftJoin($table_alias, '#__users_users', 'uu_scheduled', 'uu_scheduled.id=' . $table_alias . '.' . $user_field);
        }

        $query_str = $query->getRawQuery();
        //  print_r($query_str); exit;

        return $query_str;
    }

    public function getWhereQuery($query, $sheduled, $table_alias, $date_field) {

        $wait_period = $sheduled->wait_period;
        $is_repeated = $sheduled->is_repeated;
        $repeat_after = ($sheduled->repeat_after) ? $sheduled->repeat_after : 1;
        $repeat_stop = ($sheduled->repeat_stop) ? $sheduled->repeat_stop : 10;

        $field_name = $table_alias . '.' . $date_field;
        $start_date = date('Y-m-d', strtotime('-' . $wait_period . ' days'));
        $start_period = $start_date . ' 00:00:00';
        $end_period = $start_date . ' 23:59:59';

        $query->orWhere($field_name . ' BETWEEN \'' . $start_period . '\' AND \'' . $end_period . '\'');

        if ($is_repeated && $repeat_stop) {

            for ($x = 1; $x <= $repeat_stop; $x++) {

                $start_date = date('Y-m-d', strtotime('-' . ($x * $repeat_after + $wait_period) . ' days'));
                $start_period = $start_date . ' 00:00:00';
                $end_period = $start_date . ' 23:59:59';

                $query->orWhere($field_name . ' BETWEEN \'' . $start_period . '\' AND \'' . $end_period . '\'');
            }
        }
    }

    public function getScheduledList() {

        $query = new Query();
        $query->select('nns.*');
        $query->from('#__notification_newsletters_scheduled', 'nns');
        $query->andWhere('nns.published=1');
        $query->andWhere('nns.scheduled_newsletter_date < NOW()');

        $records = $query->loadObjectList();


        return $records;
    }

    public function getTableFieldInputList() {

        $doctrine = $this->container->get('doctrine');

        $extension_path = $this->request->get('extension_path');
        $table_name = str_replace('/', '_', $extension_path);

        $tmp_array = array();

        $entityManager = $doctrine->getEntityManager();
        $columns = $entityManager->getConnection()->getSchemaManager()->listTableColumns($doctrine->prefix . $table_name);

        foreach ($columns as $column) {
            $tmp_array[] = $column->getName();
        }

        return $tmp_array;
    }

}
