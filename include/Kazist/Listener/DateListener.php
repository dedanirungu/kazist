<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DateListener
 *
 * @author sbc
 */

namespace Kazist\Listener;

defined('KAZIST') or exit('Not Kazist Framework');

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Kazist\Service\Form\InputEvent;

class DateListener implements EventSubscriberInterface {

    public $container = '';

    public function onDateValidation(InputEvent $event) {

        global $sc;

        $this->container = $sc;

        $form_data = $event->getFormData();
        $input_name = $event->getInputName();
        $mysql_type = $event->getMysqlType();

        $date_str = 'Y-m-d';
        $date = $form_data[$input_name];

        $d = \DateTime::createFromFormat($date_str, $date);

        return $d && strtotime($d->format($date_str)) == strtotime($date);
    }

    public static function getSubscribedEvents() {
        return array('input.date.validation' => 'onDateValidation');
    }

}
