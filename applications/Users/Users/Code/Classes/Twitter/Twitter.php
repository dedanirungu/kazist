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
 * Description of Twitter
 *
 * @author sbc
 */
class Twitter {

    public function getTwitterObject($is_return = false) {
        require_once JPATH_ROOT . '/com_kazisocial/classes/twitteroauth/twitteroauth/twitteroauth.php';

        jimport('joomla.application.component.helper');
        $config = JComponentHelper::getParams('com_kazisocial');

        $consumer_key = $config->get('twitter_costumer_key');
        $consumer_secret = $config->get('twitter_costumer_secret');
        $access_token = $config->get('twitter_access_token');
        $access_secret = $config->get('twitter_access_secret');

        $session = JFactory::getSession();

        $oauth_token = $session->get('oauth_token');
        $oauth_token_secret = $session->get('oauth_token_secret');

        // The TwitterOAuth instance
        if ($is_return) {
            $twitteroauth = new TwitterOAuth($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret);
        } else {
            $twitteroauth = new TwitterOAuth($consumer_key, $consumer_secret);
        }


        return $twitteroauth;
    }

    public function getTwitterAuthPage() {
        $app = JFactory::getApplication();
        $session = JFactory::getSession();

        $uri = JURI::getInstance();
        $url = $uri->root();

        $twitteroauth = $this->getTwitterObject();
        // Requesting authentication tokens, the parameter is the URL we will be redirected to
        $request_token = $twitteroauth->getRequestToken($url . 'index.php?option=com_kazisocial&task=registration.twitter');
        // Saving them into the session
        $session->set('oauth_token', $request_token['oauth_token']);
        $session->set('oauth_token_secret', $request_token['oauth_token_secret']);

        // If everything goes well..
        if ($twitteroauth->http_code == 200) {
            // Let's generate the URL and redirect
            $url = $twitteroauth->getAuthorizeURL($request_token['oauth_token'], false);
            $url = $url . '&authorize=1&force=1';
            $app->redirect($url);
        } else {
            $msg = 'Something wrong happened';
            // It's a bad idea to kill the script, but we've got to know when there's an error.
            $app->redirect('index.php?option=com_kazisocial.registration', $msg);
        }
    }

    public function completeTwitterLogin() {
        require_once(JPATH_ROOT . '/com_kazisocial/classes/kazisocial.php');

        $kazisocial = new Kazisocial;
        $app = JFactory::getApplication();
        $session = JFactory::getSession();

        $twitteroauth = $this->getTwitterObject(true);
        $ouath_verifier = JRequest::getVar('oauth_verifier');
        $oauth_token = $session->get('oauth_token');
        $oauth_token_secret = $session->get('oauth_token_secret');

        if (!empty($ouath_verifier) && !empty($oauth_token) && !empty($oauth_token_secret)) {
            // Let's request the access token
            $access_token = $twitteroauth->getAccessToken($ouath_verifier);
            // Save it in a session var
            $session->get('access_token', $access_token);
            // Let's get the user's info
            $user_info = $twitteroauth->get('account/verify_credentials');

            $screen_name = strtolower($user_info->screen_name);
            $username = $kazisocial->getUsername($screen_name);
            $name = $user_info->name;
            $user_info_id = $user_info->id;

            $user = array();
            $user['name'] = $user_info->name;
            $user['username'] = $username;
            $user['password'] = $username;

            $kazisocial->autoLoginNUpdate($user_info_id, 'twitter', $user);
        } else {
            $app->redirect('index.php?option=com_kazisocial&task=registration.twitter');
        }
    }

}
