<?php

namespace Kazist\Service\Email;

defined('KAZIST') or exit('Not Kazist Framework');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Kazist\KazistFactory;
use Kazist\Service\StringModification;
use Kazist\Service\Database\Query;
use Kazist\Service\System\System;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\DNSCheckValidation;
use Egulias\EmailValidator\Validation\MultipleValidationWithAnd;
use Egulias\EmailValidator\Validation\RFCValidation;

//use Kazist\Service\Email\Phpmailer\PHPMailer;


class EmailSender {

    var $from_name = '';
    var $from_email = '';
    var $is_html = true;
    var $debug_exit = false;
    var $use_template = true;
    var $parameter_name = '';
    var $parameters = array();
    var $container = '';
    var $request = '';
    var $mailer = '';
    var $anti_flood = 100;
    var $throttler = 100;
    var $sql_limit = 40;
    var $sql_offset = 0;
    var $send_completed = 0;
    var $has_error = false;
    var $error_message = '';
    var $logger = '';
    var $gateway = '';

    public function __construct($container, $request) {

        $this->container = $container;
        $this->request = $request;

        $this->mailer = $this->getMailer();
    }

    public function sendEmailList($priority = '') {

        $factory = new KazistFactory();

        $records = $this->getEmailList($priority);

        if (!empty($records)) {
            foreach ($records as $record) {

                $this->has_error = false;
                $this->sql_offset = (int) $record->sent_counter;
                $priority = $record->priority;

                $subject = $record->subject;
                $body = $record->body;
                $recipients = json_decode($record->recipients);
                $parameters = json_decode($record->parameters);
                $attachments = json_decode($record->attachments);

                $this->preSendParameterize($subject, $body, $recipients, $parameters, $attachments, $priority);

                $this->sql_offset = (int) $record->sent_counter + $this->sql_limit;
                $record->sent_counter = $this->sql_offset;
                $record->completed = $this->send_completed;

                if ($this->send_completed) {
                    $factory->deleteRecords('#__notification_emails', array('id=:id'), array('id' => $record->id));
                } else {
                    $record->uniq_name = NULL;
                    $record->send_date = date('Y-m-d H:i:s');
                    $factory->saveRecord('#__notification_emails', $record);
                }
            }
        }
    }

    public function getEmailList($priority = '') {

        $factory = new KazistFactory();

        $query = $factory->getQueryBuilder('#__notification_emails', 'ne');
        if ((int) $priority) {
            $query->andWhere('ne.priority <= :priority');
            $query->andWhere('ne.uniq_name = :uniq_name');
            $query->setParameter('uniq_name', session_id());
            $query->setParameter('priority', $priority);
        } else {
            $query->andWhere('ne.completed=0 OR ne.completed IS NULL');
            $query->andWhere('ne.send_date < :send_date');
            $query->setParameter('send_date', date('Y-m-d H:i:s'));
        }
        $query->orderBy('ne.priority', 'ASC');
        $query->addOrderBy('ne.date_created', 'ASC');
        $query->setMaxResults($this->sql_limit);

        $records = $query->loadObjectList();


        foreach ($records as $record) {

            $record->send_date = date('Y-m-d H:i:s', strtotime('+15 minutes'));

            $factory->saveRecord('#__notification_emails', $record);
        }


        return $records;
    }

    public function sendEmail_($subject, $body, $recipient, $attachments) {

        require_once JPATH_ROOT . 'vendor/phpmailer/phpmailer/PHPMailerAutoload.php';

        $mail = new \PHPMailer;

        $mail->SMTPDebug = 4;                                               // Enable verbose debug output

        $mail->Host = $this->gateway->smtp_host;                            // Specify main and backup SMTP servers
        $mail->Username = $this->gateway->smtp_username;                    // SMTP username
        $mail->Password = $this->gateway->smtp_password;                    // SMTP password
        $mail->Port = $this->gateway->smtp_port;                            // TCP port to connect to

        if ($this->gateway->smtp_auth) {
            $mail->SMTPAuth = true;                                         // Enable SMTP authentication
            $mail->SMTPSecure = $this->gateway->smtp_secure;            // Enable TLS encryption, `ssl` also accepted
        }
        if ($this->gateway->type == 'smtp') {
            $mail->isSMTP();                                                // Set mailer to use SMTP
        }

        $mail->setFrom($this->from_email, $this->from_name);
        $mail->addAddress($recipient);                                      // Name is optional
        $mail->addReplyTo($this->from_email, $this->from_name);

        $mail->isHTML(true);

        $new_body = $this->getCssInlined($body);
        $new_body = $this->getImageReformated($body);
        $breaks = array("<br />", "<br>", "<br/>");
        $plain = str_ireplace($breaks, "\r\n", $new_body);
        $plain = strip_tags($plain);

        $mail->Subject = $subject;
        $mail->Body = $new_body;
        $mail->AltBody = $plain;

        if (!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo 'Message has been sent';
        }
    }

    public function sendEmail($subject, $body, $recipient, $attachments) {

        require_once JPATH_ROOT . 'vendor/swiftmailer/swiftmailer/lib/swift_required.php';

        $new_body = $this->getCssInlined($body);
        $new_body = $this->getImageReformated($new_body);

        $validator = new EmailValidator();
        $multipleValidations = new MultipleValidationWithAnd([
            new RFCValidation(),
            new DNSCheckValidation()
        ]);

        if ($validator->isValid($recipient, $multipleValidations)) {

            $breaks = array("<br />", "<br>", "<br/>");
            $plain = str_ireplace($breaks, "\r\n", $new_body);
            $plain = strip_tags($plain);

            $message = new \Swift_Message($subject);

            $message->setSubject($subject);
            $message->setTo($recipient);
            $message->setContentType("text/plain; charset=UTF-8");
            $message->setBody($plain, 'text/plain');
            $message->addPart($new_body, 'text/html');

            if ($attachments) {
                foreach ($attachments as $attachment) {

                    $attachment_arr = explode('/', $attachment);

                    $attachment_arr_rev = array_reverse($attachment_arr);

                    $message->attach(
                            \Swift_Attachment::fromPath($attachment)->setFilename($attachment_arr_rev[0])
                    );
                }
            }

            $this->sendMessage($message);
        }
    }

    public function preSendProcessing($subject, $body, $recipient, $parameters = array(), $attachments = array(), $priority = '') {

        $email = new Email();

        $recipient = trim($recipient);

        $email->saveEmailToHarvester($recipient);
        $is_allowed = $email->canReceiveEmails($recipient);

        try {

            $new_subject = $email->processParameters($subject, $parameters);
            $new_body = $email->processParameters($body, $parameters);

            $parameters['recipient'] = $recipient;
            $prepared_body = $email->prepareBody($new_subject, $new_body, $parameters);
        } catch (\Exception $ex) {
            $this->error_message = $ex->getMessage() . $this->logger->dump();
            $this->has_error = true;
            return false;
        }

        if ($new_subject == '') {
            return false;
        }

        if ($is_allowed) {
            $this->sendEmail($new_subject, $prepared_body, $recipient, $attachments);
        }

        return true;
    }

    public function preSendParameterize($subject, $body, $recipients = array(), $parameters = array(), $attachments = array(), $priority = 7) {

        $email = new Email();

        $email->sql_offset = $this->sql_offset;
        $email->sql_limit = $this->sql_limit;

        $new_parameters = $email->getParameters($parameters);
        $new_recipients = $email->getRecipients($recipients);
        // print_r($new_parameters); exit;

        if (empty($new_recipients) && empty($new_parameters)) {
            $this->send_completed = 1;
        } elseif (!empty($new_recipients)) {

            $this->send_completed = (count($new_recipients) != $this->sql_limit) ? 1 : 0;

            foreach ($new_recipients as $key => $recipient) {

                $recipient_email = $email->getEmailFromArray($recipient);

                if ($recipient_email <> '') {
                    if (!$this->preSendProcessing($subject, $body, $recipient_email, $new_parameters, $attachments, $priority)) {
                        break;
                    }
                }
            }
        } elseif (!empty($new_parameters) && isset($new_parameters[0])) {

            $this->send_completed = (count($new_parameters) != $this->sql_limit) ? 1 : 0;

            foreach ($new_parameters as $key => $parameter) {

                $recipient_email = $email->getEmailFromArray($parameter);

                if ($recipient_email <> '') {
                    if (!$this->preSendProcessing($subject, $body, $recipient_email, $parameter, $attachments, $priority)) {
                        break;
                    }
                }
            }
        } elseif (!empty($new_parameters) && !isset($new_parameters[0])) {

            $this->send_completed = 1;

            $recipient_email = $email->getEmailFromArray($new_parameters);

            if ($recipient_email <> '') {
                $this->preSendProcessing($subject, $body, $recipient_email, $new_parameters, $attachments, $priority);
            }
        } else {
            $this->error_message = 'Parameters Used do not have Email to send to or are Null';
        }

        if ($this->has_error) {
            $new_subject = 'Error Email Not sent :- ' ;
            $new_body = '<h1>Error Not sent</h1> '
                    . '<br>===========================================================================<br>'
                    . '<b>Error:</b> '
                    . '<br>=======================================<br>'
                    . $this->error_message
                    . '<br>===========================================================================<br>'
                    . '<b>Header:</b> '
                    . '<br>=======================================<br>'
                    . $body
                    . '<br>===========================================================================<br>'
                    . '<b>Body:</b> '
                    . '<br>=======================================<br>'
                    . $subject
                    . '<br>=======================================<br>'
                    . '<br>===========================================================================<br>'
            ;

            $this->sendEmail($new_subject, $new_body, 'dedanirungu@gmail.com');

            //$this->send_completed = true;

            return;
        }
    }

    public function sendMessage($message) {

        $failedRecipients = array();

        $factory = new KazistFactory();
        $mailer = $this->getMailer();

        $message->setReplyTo($this->from_email);
        $message->setSender($this->from_email);
        $message->setFrom(array($this->from_email => $this->from_name));

        try {
            if (!$mailer->send($message, $failedRecipients)) {
                print_r($failedRecipients);
                throw new \Exception('Error While sending Emails.' . json_encode($failedRecipients));
            } else {
                $factory->enqueueMessage('Email sent successfully');
            }
        } catch (Exception $e) {
            echo $this->logger->dump();
            echo $e->getTraceAsString();
        }
    }

    public function getMailer() {

        /*
         * setting
         * anti_flood for reconnecting smtp
         * throtter for controlling emails per minute
         */

        if (is_object($this->mailer)) {
            return $this->mailer;
        }

        $transport = $this->getMailTransport();
        $this->mailer = new \Swift_Mailer($transport);

        // Use AntiFlood to re-connect after 100 emails and second (30)
        $this->mailer->registerPlugin(new \Swift_Plugins_AntiFloodPlugin($this->anti_flood));

        // Rate limit to 100 emails per-minute
        $this->mailer->registerPlugin(new \Swift_Plugins_ThrottlerPlugin(
                $this->throttler, \Swift_Plugins_ThrottlerPlugin::MESSAGES_PER_MINUTE
        ));

        // To use the ArrayLogger
        $this->logger = new \Swift_Plugins_Loggers_ArrayLogger();
        //$this->logger = new \Swift_Plugins_Loggers_EchoLogger();
        $this->mailer->registerPlugin(new \Swift_Plugins_LoggerPlugin($this->logger));

        return $this->mailer;
    }

    public function getMailTransport() {

        require_once JPATH_ROOT . 'vendor/swiftmailer/swiftmailer/lib/swift_required.php';

        $gateway = $this->getEmailGateway();

        $this->anti_flood = $gateway->anti_flood;
        $this->throttler = $gateway->throttler;
        $this->from_email = $gateway->from_email;
        $this->from_name = $gateway->from_name;

        if ($gateway->type == 'smtp') {

            $transport = new \Swift_SmtpTransport();
            $transport->setHost($gateway->smtp_host);
            $transport->setPort($gateway->smtp_port);
            $transport->setUsername($gateway->smtp_username);
            $transport->setPassword($gateway->smtp_password);
            $transport->setAuthMode('login');

            if ($gateway->smtp_auth) {
                $transport->setEncryption($gateway->smtp_secure);
            }
        } elseif ($gateway->type == 'sendmail') {

            if ($gateway->sendmail_command <> '') {
                $transport = new Swift_SendmailTransport($gateway->sendmail_command);
            } else {
                $transport = new Swift_SendmailTransport();
            }
        } else {
            $transport = new \Swift_SmtpTransport();
        }

        return $transport;
    }

    private function getEmailGateway() {

        $query = new Query();
        $query->select('*');
        $query->from('#__notification_gateways');
        $query->where('published=1');

        $record = $query->loadObject();

        if (is_object($record)) {
            $this->gateway = $record;
            return $record;
        } else {
            return false;
        }
    }

    public function canReceiveEmails($email) {

        $factory = new KazistFactory();
        $db = $factory->getDatabase();

        $sending_email = $factory->getSetting('subscriber_subscriber_sending_email');

        $query = $db->getQuery(true);
        $query->select('ns.email, ns.published, '
                . '(SELECT COUNT(*) FROM #__notification_subscriber_blacklist WHERE subscriber_id=ns.id) AS blacklist, '
                . ' (SELECT COUNT(*) FROM #__notification_subscriber_whitelist WHERE subscriber_id=ns.id) AS whitelist '
        );
        $query->from('#__notification_subscriber AS ns');
        $query->where('ns.email=' . $db->quote($email));
        $db->setQuery($query);

        $record = $db->loadObject();

        if ($record->whitelist) {
            return true;
        } elseif ($record->published) {
            return false;
        } elseif ($record->blacklist) {
            return false;
        } elseif ($sending_email) {
            return true;
        }

        return false;
    }

    public function getCssInlined($body) {

        $css = file_get_contents(JPATH_ROOT . 'assets/css/email.css');

        try {

            $class_name = 'TijsVerkoyen\CssToInlineStyles\CssToInlineStyles';

            if (class_exists($class_name)) {

                $cssToInlineStyles = new $class_name();

                $body = $cssToInlineStyles->convert(
                        $body, $css
                );
            }

            return $body;
        } catch (\Exception $exc) {
            return $body;
        }
    }

    public function getImageReformated($body) {

        $system = new System();

        $body = $system->processImagesUrl($body);

        return $body;
    }

}
