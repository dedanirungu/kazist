<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Notification\Newsletters\Automated\Code\Models;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\Model\BaseModel;
use Kazist\KazistFactory;
use Kazist\Service\Email\Email;
use Cron\CronExpression;
use Kazist\Service\System\System;
use Kazist\Service\Database\Query;
use Kazist\Service\Json\Json;

/**
 * Description of AutomatedModel
 *
 * @author sbc
 */
class AutomatedModel extends BaseModel {

    //put your code here

    public function sendAutomatedList() {

        $autonewsletters = $this->getAutomatedList();

        if (!empty($autonewsletters)) {
            foreach ($autonewsletters as $autonewsletter) {
                $this->sendAutomated($autonewsletter);
            }
        }
    }

    private function sendAutomated($autonewsletter) {

        $email = new Email();
        $factory = new KazistFactory();
        $frequency = $this->getFrequency($autonewsletter->frequency_id);

        $email->parameter_name = $autonewsletter->parameter_name;

        if (is_object($frequency)) {

            $subject = $autonewsletter->subject;
            $body = $autonewsletter->body;

            $data_query_str = $this->getItemData($autonewsletter);
            $recipient_query_str = $this->getQueryStr($autonewsletter);

            $this->updateAutomatedData($autonewsletter, $frequency);

            $email->priority = 7;
            $email->sendPreparedEmail($subject, $body, $recipient_query_str, $data_query_str);

            $factory->saveRecord('#__notification_newsletters_automated', $autonewsletter);
        }
    }

    function updateAutomatedData($autonewsletter, $frequency) {


        $next_cron_str = $frequency->repeated_minute . ' '
                . $frequency->repeated_hour . ' '
                . $frequency->repeated_day_of_month . ' '
                . $frequency->repeated_month . ' '
                . $frequency->repeated_day_of_week . ' '
                . $frequency->repeated_year . ' '
        ;

        $cron = CronExpression::factory($next_cron_str);
        $next_send_time = $cron->getNextRunDate()->format('Y-m-d H:i:s');

        $autonewsletter->send_date = $next_send_time;

        return $autonewsletter;
    }

    private function getFrequency($frequency_id) {

        $factory = new KazistFactory();


        $query = new Query();
        $query->select('nnf.*');
        $query->from('#__notification_newsletters_frequencies', 'nnf');
        if ($frequency_id) {
            $query->where('id=:id');
            $query->setParameter('id', $frequency_id);
        } else {
            $query->where('1=-1');
        }


        $record = $query->loadObject();

        return $record;
    }

    private function getAutomatedList() {

        $factory = new KazistFactory();

        $query = new Query();
        $query->select('nna.*');
        $query->from('#__notification_newsletters_automated', 'nna');
        $query->where('(send_date < NOW() OR send_date IS NULL OR send_date="0000-00-00 00:00:00")');
        $query->andWhere('published=1');

        $records = $query->loadObjectList();

        return $records;
    }

    function getItemData($autonewsletter) {
        $factory = new KazistFactory();

        $table_alias = $this->getTableAlias($autonewsletter->table_name);
        $query = $factory->getQueryBuilder($autonewsletter->table_name, $table_alias);
        $query->orderBy('id', 'DESC');
        $query->setMaxResults(6);

        $query_str = base64_encode((string) $query);

        return $query_str;
    }

    function getQueryStr() {

        $factory = new KazistFactory();

        $query = new Query();
        $query->select('nnm.*, nnm.email as recipient');
        $query->from('#__notification_subscribers', 'nnm');

        $query_str = base64_encode((string) $query);

        return $query_str;
    }

}
