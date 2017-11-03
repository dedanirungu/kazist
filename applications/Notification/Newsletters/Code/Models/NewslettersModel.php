<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Notification\Newsletters\Code\Models;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\Model\BaseModel;
use Kazist\KazistFactory;
use Kazist\Service\Email\Email;
use Cron\CronExpression;
use Kazist\Service\Database\Query;

/**
 * Model to get data for the issue list view
 *
 * @since  1.0
 */
class NewslettersModel extends BaseModel {

    function getTemplate() {

        $factory = new KazistFactory();

        $template_id = $this->request->get('template_id');

        $query = new Query();
        $query->select('nt.*');
        $query->from('#__notification_templates', 'nt');
        $query->where('1=1');
        if ($template_id) {
            $query->andWhere('nt.id=:template_id');
            $query->setParameter('template_id', $template_id);
        }

        $record = $query->loadObject();
        // print_r($record->template); exit;

        if (is_object($record)) {
            $this->request->request->set('body', $record->template);
            return $record->template;
        }

        return '';
    }

    function save($form) {

        set_time_limit(0);

        $form = (!empty($form)) ? $form : $this->request->get('form');

        if ($form['how_to_repeat'] == '') {
            $form['repeated_every'] = '';
            $form['forever'] = '';
            $form['repeated'] = '';
            $form['repeated_minute'] = '';
            $form['repeated_hour'] = '';
            $form['repeated_day_of_month'] = '';
            $form['repeated_month'] = '';
            $form['repeated_day_of_week'] = '';
            $form['repeated_year'] = '';
        }

        $newsletter_id = parent::save($form);

        $this->sendNewsletter($newsletter_id);

        return $newsletter_id;
    }

    function sendNewsletterList() {

        $newsletters = $this->getNewsletters();

        if (!empty($newsletters)) {
            foreach ($newsletters as $newsletter) {
                $this->sendNewsletter('', $newsletter);
            }
        }
    }

    function sendNewsletter($newsletter_id, $newsletter = '') {

        $email = new Email();
        $factory = new KazistFactory();

        //  $email->use_template = false;

        if (!is_object($newsletter)) {
            $newsletter = $this->getNewsletter($newsletter_id);
        } else {
            $newsletter_id = $newsletter->id;
        }

        $group_ids = $this->getNewsletterGroups($newsletter_id);
        $can_sent = $this->checkNewsletterSendTime($newsletter);

        if ($can_sent) {

            $subject = $newsletter->subject;
            $body = $newsletter->body;
            $email->priority = 7;
            $email->send_date = $newsletter->send_date;

            $parameters = $this->getParametersArray($group_ids);

            $email->sendPreparedEmail($subject, $body, '', $parameters);
        }
    }

    function checkNewsletterSendTime($newsletter) {

        $send_now = false;

        $factory = new KazistFactory();

        $default_date_stamp = strtotime('2000-01-01');
        $send_date_stamp = strtotime($newsletter->send_date);
        $start_date_stamp = strtotime($newsletter->start_date);
        $end_date_stamp = strtotime($newsletter->end_date);

        $is_start_time = ($start_date_stamp && $start_date_stamp < time()) ? true : false;
        $is_end_time = ($end_date_stamp && $end_date_stamp < time()) ? true : false;

        if ((int) $newsletter->repeated && $start_date_stamp && $end_date_stamp && $start_date_stamp > $default_date_stamp && $end_date_stamp > $default_date_stamp) {

            if ($is_start_time && !$is_end_time) {
                if (($start_date_stamp <= $send_date_stamp) && ($send_date_stamp <= $end_date_stamp)) {
                    $send_now = ($send_date_stamp < time()) ? true : false;
                }
            } elseif ($is_start_time && !$end_date_stamp) {
                if ($start_date_stamp <= $send_date_stamp) {
                    $send_now = ($send_date_stamp < time()) ? true : false;
                }
            } elseif (!$is_end_time && !$start_date_stamp) {
                if ($send_date_stamp <= $end_date_stamp) {
                    $send_now = ($send_date_stamp < time()) ? true : false;
                }
            }
        } else {
            $send_now = ($send_date_stamp <= time()) ? true : false;
        }

        if ($newsletter->is_sent) {
            $send_now = false;
            $factory->enqueueMessage('Newsletter Already sent and closed. Set "is sent" to "No".', 'error');
        } else {
            $factory->enqueueMessage('Newsletter Prepared for sending. Some Emails will be added to the queue and sent later', 'info');
        }

        return $send_now;
    }

    function updateNewsletterData($newsletter) {

        $factory = new KazistFactory();

        $newsletter = clone $newsletter;

        if ($newsletter->how_to_repeat) {

            $next_cron_str = $newsletter->repeated_minute . ' '
                    . $newsletter->repeated_hour . ' '
                    . $newsletter->repeated_day_of_month . ' '
                    . $newsletter->repeated_month . ' '
                    . $newsletter->repeated_day_of_week . ' '
                    . $newsletter->repeated_year . ' '
            ;

            $cron = CronExpression::factory($next_cron_str);
            $next_send_time = $cron->getNextRunDate()->format('Y-m-d H:i:s');

            $newsletter->send_date = $next_send_time;
        } else {
            $newsletter->is_sent = 1;
        }

        $factory->saveRecord('#__notification_newsletters', $newsletter, array('id=:id'), array('id' => $newsletter->id));

        return $newsletter;
    }

    function getNewsletterGroups($newsletter_id) {

        $tmp_array = array();

        $factory = new KazistFactory();


        $query = new Query();
        $query->select('nnn.group_id');
        $query->from('#__notification_newsletters_groups', 'nnn');
        $query->where('nnn.newsletter_id=:newsletter_id');
        $query->setParameter('newsletter_id', $newsletter_id);

        $records = $query->loadObjectList();

        if (!empty($records)) {
            foreach ($records as $record) {
                $tmp_array[] = $record->group_id;
            }
        }

        return $tmp_array;
    }

    function getNewsletters() {

        $factory = new KazistFactory();

        $query = new Query();
        $query->select('nn.*');
        $query->from('#__notification_newsletters', 'nn');
        $query->where('(is_sent=0 OR is_sent IS NULL)');
        $query->andWhere('published=1');
        $query->andWhere('(send_date=NOW() OR send_date IS NULL OR send_date="0000-00-00 00:00:00")');

        $records = $query->loadObjectList();

        if (!empty($records)) {
            foreach ($records as $key => $record) {
                $this->updateNewsletterData($record);
            }
        }

        return $records;
    }

    function getNewsletter($newsletter_id) {

        $factory = new KazistFactory();


        $query = new Query();
        $query->select('nn.*');
        $query->from('#__notification_newsletters', 'nn');
        $query->where('id=:newsletter_id');
        $query->setParameter('newsletter_id', $newsletter_id);

        $record = $query->loadObject();

        $this->updateNewsletterData($record);

        return $record;
    }

    function getParametersArray($group_ids) {

        $factory = new KazistFactory();

        $query = new Query();
        $query->select('DISTINCT ns.*, ns.email as recipient');
        $query->from('#__notification_subscribers', 'ns');
        $query->leftJoin('ns', '#__notification_groups_subscribers', 'ngs', 'ns.id=ngs.subscriber_id');

        if (!empty($group_ids)) {
            $query->where('ngs.group_id IN (' . implode(', ', $group_ids) . ')');
        }

        $query->orderBy('ns.email');

        $query_str = base64_encode((string) $query);

        return $query_str;
    }

}
