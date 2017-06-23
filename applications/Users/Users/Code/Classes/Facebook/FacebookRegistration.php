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

        $factory = new KazistFactory();
        $session = $factory->getSession();

        $url = $session->get('facebook_registration_url');

        if ($url == '') {

            $facebook = new Facebook();
            $url = $facebook->getFacebookUrl();

            $session->set('facebook_registration_url', $url);
        }
        return $url;
    }

    public function completeFacebookLogin() {
        require_once JPATH_ROOT . '/com_kazisocial/classes/facebook.php';

        $kazifacebook = new Kazifacebook();
        $url = $kazifacebook->completeFacebookLogin();

        return $url;
    }

}
