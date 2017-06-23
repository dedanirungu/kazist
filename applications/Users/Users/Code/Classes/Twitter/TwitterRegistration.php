<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace User\Users\Code\Classes\Twitter;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\KazistFactory;

/**
 * Description of Yahoo
 *
 * @author sbc
 */
class TwitterRegistration {

    public function getTwitterUrl() {

        $url = 'index.php?option=com_kazisocial&task=registration.twitter_auth_page';

        return $url;
    }

    public function getTwitterAuthPage() {

        $session = JFactory::getSession();

        require_once JPATH_ROOT . '/com_kazisocial/classes/twitter.php';

        $kazitwitter = new KaziTwitter();
        $url = $kazitwitter->getTwitterAuthPage();

        return $url;
    }

    public function completeTwitterLogin() {

        require_once JPATH_ROOT . '/com_kazisocial/classes/twitter.php';

        $kazitwitter = new KaziTwitter();
        $url = $kazitwitter->completeTwitterLogin();

        return $url;
    }

}
