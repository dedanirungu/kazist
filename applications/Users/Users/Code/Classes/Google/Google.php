<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Users\Users\Code\Classes\Google;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\KazistFactory;

/**
 * Description of Google
 *
 * @author sbc
 */
class Google {

    public $container = '';
    public $request = '';

    public function __construct() {
        global $sc;

        $this->container = $sc;
        $this->request = $this->container->get('request');
    }

    public function getGoogleObject($action) {

        require_once JPATH_ROOT . 'libraries/google/Google_Client.php';
        require_once JPATH_ROOT . 'libraries/google/contrib/Google_Oauth2Service.php';

        $factory = new KazistFactory();
        $session = $factory->getSession();
        $uri = $session->get('uri');
        $url = $uri->base->full;

        $google_client_id = $factory->getSetting('google_client_id');
        $google_client_secret = $factory->getSetting('google_client_secret');
        $google_developer_key = $factory->getSetting('google_developer_key');
        $google_label = $factory->getSetting('google_label');

        $google_redirect_url = $factory->generateUrl('users.users.googlereturn', array('action' => $action));


        $gClient = new \Google_Client();
        $gClient->setApplicationName($google_label);
        $gClient->setClientId($google_client_id);
        $gClient->setClientSecret($google_client_secret);
        $gClient->setRedirectUri($google_redirect_url);
        $gClient->setDeveloperKey($google_developer_key);


        $array_scope = array('https://www.googleapis.com/auth/plus.login',
            'https://www.googleapis.com/auth/plus.me',
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile',
            'https://www.google.com/m8/feeds/'
        );


        if ($action == 'invite') {
            $array_scope[] = 'https://www.google.com/m8/feeds/contacts/default/full';
            $array_scope[] = 'https://www.googleapis.com/auth/contacts.readonly';
        }

        $gClient->setScopes($array_scope);

        $google_oauthV2 = new \Google_Oauth2Service($gClient);

        return array($gClient, $google_oauthV2, $google_redirect_url);
    }

    public function getGoogleAuthPage($action = 'registration') {

        $session = $this->container->get('session');
        require_once JPATH_ROOT . '/libraries/google/io/Google_HttpRequest.php';


        $factory = new KazistFactory();

        $authUrl = '';
        $reset = $this->request->request->get('reset');
        $code = $this->request->request->get('code');
        $token = $session->get('token');

        list($gClient, $google_oauthV2, $google_redirect_url) = $this->getGoogleObject($action);

        if (isset($reset)) {
            $session->clear('token');
            $gClient->revokeToken();
            $factory->redirect($google_redirect_url);
        }

        if (isset($code)) {
            $gClient->authenticate($code);
            $session->set('token', $gClient->getAccessToken());
            $factory->redirect($google_redirect_url);
            return;
        }


        if (!$gClient->getAccessToken()) {
            $authUrl = $gClient->createAuthUrl();
        }

        return $authUrl;
    }

    public function completeGoogleLogin() {
        require_once JPATH_ROOT . '/com_kazisocial/classes/google/io/Google_HttpRequest.php';
        require_once JPATH_ROOT . '/com_kazisocial/classes/kazisocial.php';

        $authUrl = '';
        $kazisocial = new Kazisocial;
        $app = JFactory::getApplication();
        $db = JFactory::getDBO();
        $session = JFactory::getSession();

        $reset = JRequest::getVar('reset');
        $code = JRequest::getVar('code');
        $token = $session->get('token');

        list($gClient, $google_oauthV2, $google_redirect_url) = $this->getGoogleObject('registration');

        print_r($google_redirect_url);
        exit;
        if (isset($reset)) {
            $session->clear('token');
            $gClient->revokeToken();
            $app->redirect(filter_var($google_redirect_url, FILTER_SANITIZE_URL));
        }

        if (isset($code)) {
            $gClient->authenticate($code);
            $session->set('token', $gClient->getAccessToken());
            $app->redirect(filter_var($google_redirect_url, FILTER_SANITIZE_URL));
            return;
        }

        if (isset($token)) {
            $gClient->setAccessToken($token);
        }

        if ($gClient->getAccessToken()) {

            $google_user = $google_oauthV2->userinfo->get();


            $name = filter_var($google_user['name'], FILTER_SANITIZE_SPECIAL_CHARS);
            $email = filter_var($google_user['email'], FILTER_SANITIZE_EMAIL);
            $user_info_id = $google_user['id'];

            $email_exploded = explode('@', $email);

            $session->set('token', $gClient->getAccessToken());

            $username = $kazisocial->getUsername($email_exploded[0]);

            $user['name'] = $name;
            $user['username'] = $username;
            $user['email'] = $email;
            $user['password'] = $username;

            $kazisocial->autoLoginNUpdate($user_info_id, 'google', $user);
        }
    }

    public function inviteFromGoogle() {
        $authUrl = '';
        $app = JFactory::getApplication();
        $session = JFactory::getSession();

        $reset = JRequest::getVar('reset');
        $code = JRequest::getVar('code');
        $token = $session->get('token');

        list($gClient, $google_oauthV2, $google_redirect_url) = $this->getGoogleObject('invite');
        if (isset($reset)) {
            $session->clear('token');
            $gClient->revokeToken();
            $app->redirect(filter_var($google_redirect_url, FILTER_SANITIZE_URL));
        }

        if (isset($code)) {
            $gClient->authenticate($code);
            $session->set('token', $gClient->getAccessToken());
            $app->redirect(filter_var($google_redirect_url, FILTER_SANITIZE_URL));
            return;
        }

        if (isset($token)) {
            $gClient->setAccessToken($token);
        }


        if ($gClient->getAccessToken()) {
            $req = new Google_HttpRequest("https://www.google.com/m8/feeds/contacts/default/full?max-results=9999");

//For logged in user, get details from google using access token
            $val = $gClient->getIo()->authenticatedRequest($req);

            $xml = new \SimpleXMLElement($val->getResponseBody());
            $xml->registerXPathNamespace('gd', 'http://schemas.google.com/g/2005');
            $result = $xml->xpath('//gd:email');

            foreach ($result as $title) {
                $emails[] = (string) $title->attributes()->address;
            }
        }

        return $emails;
    }

}
