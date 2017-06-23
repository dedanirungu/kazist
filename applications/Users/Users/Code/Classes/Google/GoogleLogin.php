<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace User\Users\Code\Classes\Google;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\KazistFactory;

/**
 * Description of Yahoo
 *
 * @author sbc
 */
class GoogleRegistration {

    public function getGoogleUrl() {

        $session = JFactory::getSession();

        $url = $session->get('google_registration_url');

        if ($url == '') {
            require_once JPATH_ROOT . '/com_kazisocial/classes/google.php';

            $kazigoogle = new KaziGoogle();
            $url = $kazigoogle->getGoogleAuthPage();

            $session->set('google_registration_url', $url);
        }

        return $url;
    }

    public function completeGoogleLogin() {

        require_once JPATH_ROOT . '/com_kazisocial/classes/google.php';

        $kazigoogle = new KaziGoogle();
        $kazigoogle->completeGoogleLogin();
    }

}
