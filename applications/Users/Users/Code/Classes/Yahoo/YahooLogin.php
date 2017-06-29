<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace User\Users\Code\Classes\Yahoo;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\KazistFactory;

/**
 * Description of Yahoo
 *
 * @author sbc
 */
class YahooRegistration {

    //put your code here

    public function getYahooUrl() {

        $session = JFactory::getSession();

        $url = $session->get('yahoo_registration_url');

        if ($url == '') {
            require_once JPATH_ROOT . '/com_kazisocial/classes/yahoo.php';

            $kaziyahoo = new KaziYahoo();
            $url = $kaziyahoo->getYahooAuthPage();

            $session->set('yahoo_registration_url', $url);
        }
        return $url;
    }

    public function completeYahooLogin() {
        require_once JPATH_ROOT . '/com_kazisocial/classes/yahoo.php';

        $kaziyahoo = new KaziYahoo();
        $kaziyahoo->completeYahooLogin();
    }

}
