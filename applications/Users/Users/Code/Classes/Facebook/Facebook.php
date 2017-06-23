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
 * Description of Facebook
 *
 * @author sbc
 */
class Facebook {

    public function getFacebookObject($action) {


        $factory = new KazistFactory();
        $session = $factory->getSession();
        $uri = $session->get('uri');
        $url = $uri->base->full;

        $facebook_app_id = $factory->getSetting('facebook_app_id');
        $facebook_secret = $factory->getSetting('facebook_secret');
        $facebook_url = $factory->generateUrl('users.users.facebookreturn', array( 'action' => $action));


        if (!class_exists('Facebook'))
            require_once JPATH_ROOT . 'libraries/facebook/facebook.php';

        $facebook = new \Facebook(array(
            'appId' => $facebook_app_id,
            'secret' => $facebook_secret,
            'allowSignedRequest' => false
        ));

        return array($facebook_url, $facebook);
    }

    public function getFacebookUrl() {



        list($facebook_url, $facebook) = $this->getFacebookObject($action);

        $params = array(
            'scope' => 'read_stream, friends_likes, email',
            'redirect_uri' => $facebook_url,
        );

        // No user, print a link for the user to login
        $login_url = $facebook->getLoginUrl($params);

        return $login_url;
    }

    public function completeFacebookLogin() {
        require_once JPATH_ROOT . '/com_kazisocial/classes/kazisocial.php';

        $kazisocial = new Kazisocial;
        $app = JFactory::getApplication();
        $db = JFactory::getDbo();
        list($facebook_url, $facebook) = $this->getFacebookObject();

        $fb_user_id = $facebook->getUser();

        if ($fb_user_id) {

            // We have a user ID, so probably a logged in user.
            // If not, we'll get an exception, which we handle below.
            try {
                $user = array();
                $user_profile = $facebook->api('/me', 'GET');

                // print_r($user_profile); exit;
                $user_info_id = $user_profile['id'];
                $name = $user_profile['name'];
                $email = $user_profile['email'];
                $email_exploded = explode('@', $email);
                $username = $kazisocial->getUsername($email_exploded[0]);

                $user['name'] = $user_profile['name'];
                $user['username'] = $username;
                $user['email'] = $email;
                $user['password'] = $username;


                $kazisocial->autoLoginNUpdate($user_info_id, 'facebook', $user);
            } catch (FacebookApiException $e) {

                $app->enqueueMessage($e->getType());
                $app->enqueueMessage($e->getMessage());
            }
        } else {

            $facebook->destroySession();
            $params = array('next' => $facebook_url . '&' . $facebook->getAccessToken());
            $url = $facebook->getLogoutUrl($params);

            $app->redirect($url);
        }
    }

}
