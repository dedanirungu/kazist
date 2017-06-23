<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Users\Users\Code\Classes\Facebook;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\KazistFactory;

/**
 * Description of Yahoo
 *
 * @author sbc
 */
class FacebookRegistration {

    public function getFacebookUrl() {

        $session = JFactory::getSession();

        $url = $session->get('facebook_registration_url');

        if ($url == '') {
            require_once JPATH_ROOT . '/com_kazisocial/classes/facebook.php';

            $kazifacebook = new Kazifacebook();
            $url = $kazifacebook->getFacebookUrl();

            $session->set('facebook_registration_url', $url);
        }
        return $url;
    }

    public function completeFacebookLogin() {
        error_reporting(E_ALL);
        require_once JPATH_ROOT . '/com_kazisocial/classes/facebook.php';

        $kazifacebook = new Kazifacebook();
        $url = $kazifacebook->completeFacebookLogin();

        return $url;
    }

}
