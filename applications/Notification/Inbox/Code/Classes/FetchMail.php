<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Notification\Inbox\Code\Classes;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\KazistFactory;
use Kazist\Service\Database\Query;

class FetchMail {

    public function fetchMail() {

        $gateways = $this->getGateways();

        foreach ($gateways as $key => $gateway) {
            $this->fetchMailViaImap($gateway);
        }
    }

    public function getGateways() {

        $factory = new KazistFactory();

        $query = $factory->getQueryBuilder('#__notification_gateways', 'ng');
        $query->andWhere('ng.published=:published');
        $query->andWhere('ng.incoming=:incoming');
        $query->setParameter('published', 1);
        $query->setParameter('incoming', 1);

        $records = $query->loadObjectList();

        return $records;
    }

    public function fetchMailViaImap($gateway) {

        $factory = new KazistFactory();

        if ($gateway->incoming_sslvalidate) {
            $url = '{' . trim($gateway->incoming_url) . ':' . trim($gateway->incoming_port) . '/imap/ssl}INBOX';
        } else {
            $url = '{' . trim($gateway->incoming_url) . ':' . trim($gateway->incoming_port) . '/imap/ssl/novalidate-cert}INBOX';
        }

        $factory->makeDir(JPATH_ROOT . 'uploads/notification/inbox/');

        // 4. argument is the directory into which attachments are to be saved:
        $mailbox = new \PhpImap\Mailbox($url, $gateway->smtp_username, $gateway->smtp_password, JPATH_ROOT . 'uploads/notification/inbox/');

        // Read all messaged into an array:
        $mailsIds = $mailbox->searchMailbox('UNSEEN');

        if (!$mailsIds) {
            die('Mailbox is empty');
        }

        // Get the first message and save its attachment(s) to disk:
        $mail = $mailbox->getMail($mailsIds[0]);

        print_r($mail);
        echo "\n\nAttachments:\n";
        print_r($mail->getAttachments());
        exit;
    }

}
