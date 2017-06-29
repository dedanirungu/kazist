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
class Yahoo {

    public function getYahooObject() {

        $uri = JURI::getInstance();
        $url = $uri->root();

        jimport('joomla.application.component.helper');
        $config = JComponentHelper::getParams('com_kazisocial');

        $customer_key = $config->get('yahoo_customer_key');
        $customer_secret = $config->get('yahoo_customer_secret');
        $appid = $config->get('yahoo_app_id');

//        $customer_key = 'dj0yJmk9UGZEeHhQTWM0c2FhJmQ9WVdrOWEwWlFiVFpCTkdFbWNHbzlNek15T1RFM01UWXkmcz1jb25zdW1lcnNlY3JldCZ4PTQz';
//        $customer_secret = 'fba18e2aa683a5ed5e1152711a352347874518cf';
//        $appid = 'GSeW6t54';

        $returnurl = $url . '/index.php?option=com_kazisocial&task=registration.yahoo';

        return array($customer_key, $customer_secret, $appid, $returnurl);
    }

    public function getYahooLink($is_first = false) {
        $session = JFactory::getSession();

        if ($_SESSION['yahoo_link'] <> '') {
            return $_SESSION['yahoo_link'];
        }

        require_once(JPATH_ROOT . '/com_kazisocial/classes/yahoo/globals.php');
        require_once(JPATH_ROOT . '/com_kazisocial/classes/yahoo/oauth_helper.php');

        list($customer_key, $customer_secret, $appid, $returnurl) = $this->getYahooObject();

// Get the request token using HTTP GET and HMAC-SHA1 signature 
        $retarr = get_request_token($customer_key, $customer_secret, $returnurl, false, true, true);

        if (!empty($retarr)) {
            list($info, $headers, $body, $body_parsed) = $retarr;

            if ($info['http_code'] == 200 && !empty($body)) {
                // print "Have the user go to xoauth_request_auth_url to authorize your app\n" . 
                //  rfc3986_decode($body_parsed['xoauth_request_auth_url']) . "\n"; 
                //echo "<pre/>"; 
                //print_r($retarr); oauth_token_secret
                $session->set('request_token', rfc3986_decode($body_parsed['oauth_token']));
                $session->set('request_token_secret', rfc3986_decode($body_parsed['oauth_token_secret']));
                $session->set('oauth_verifier', rfc3986_decode($body_parsed['oauth_token']));

                $link = urldecode($body_parsed['xoauth_request_auth_url']);

                $session->set('yahoo_link', $link);
                $_SESSION['yahoo_link'] = $link;
            }
        }

        return $_SESSION['yahoo_link'];
    }

    public function completeYahooLogin() {
        $session = JFactory::getSession();
        $user = JFactory::getUser();
        $db = JFactory::getDbo();
        $app = JFactory::getApplication();

        require_once(JPATH_ROOT . '/com_kazisocial/classes/yahoo/globals.php');
        require_once(JPATH_ROOT . '/com_kazisocial/classes/yahoo/oauth_helper.php');

        $request_token = $session->get('request_token');
        $request_token_secret = $session->get('request_token_secret');
        //$oauth_verifier = $session->get('oauth_verifier');
        $oauth_verifier = JRequest::getVar('oauth_verifier');


        list($customer_key, $customer_secret, $appid, $returnurl) = $this->getYahooObject();

        //print_r("$customer_key, $customer_secret, $appid"); exit;
        // Get the access token using HTTP GET and HMAC-SHA1 signature 
        $retarr = get_access_token($customer_key, $customer_secret, $request_token, $request_token_secret, $oauth_verifier, false, false, true);

        // print_r($retarr);
        // exit;

        if (!empty($retarr)) {
            list($info, $headers, $body, $body_parsed) = $retarr;
            if ($info['http_code'] == 200 && !empty($body)) {
                //   print "Use oauth_token as the token for all of your API calls:\n" . 
                //      rfc3986_decode($body_parsed['oauth_token']) . "\n"; 
                // Fill in the next 3 variables. 
                $guid = $body_parsed['xoauth_yahoo_guid'];
                $access_token = rfc3986_decode($body_parsed['oauth_token']);
                $access_token_secret = $body_parsed['oauth_token_secret'];

                if (!$user->id) {

                    // Call Contact API 
                    $userprofile = callcontact_yahoo($customer_key, $customer_secret, $guid, $access_token, $access_token_secret, false, true);
                    print_r($userprofile);
                    exit;

                    $email = $userprofile->profile->emails[0]->handle;

                    $email_exploded = explode('@', $email);
                    $name = $userprofile->profile->nickname;

                    $username = $this->getUsername($email_exploded[0]);

                    $tmpuser['name'] = $name;
                    $tmpuser['username'] = $username;
                    $tmpuser['email'] = $email;
                    $tmpuser['password'] = $username;


                    $this->autoLoginUser($tmpuser, 'yahoo');
                } else {

                    $members = callcontacts_yahoo($customer_key, $customer_secret, $guid, $access_token, $access_token_secret, false, true);
                    $contacts = $members->contacts->contact;

                    if (!empty($contacts)) {
                        foreach ($contacts as $contact) {
                            foreach ($contact->fields as $field) {
                                if (!is_object($field->value)) {
                                    if ($field->type == 'yahooid') {
                                        if (filter_var($field->value, FILTER_VALIDATE_EMAIL)) {
                                            $email = $field->value;
                                        } else {
                                            $email = $field->value . '@yahoo.com';
                                        }
                                    } elseif ($field->type == 'email') {
                                        $email = $field->value;
                                    }

                                    $tmpdata = new stdClass;
                                    $tmpdata->email = $email;
                                    $tmpdata->type = 'yahoo';
                                    $tmpdata->created_by = $user->id;
                                    $tmpdata->date_created = date('Y-m-d');

                                    $record_exist = $this->checkInviteExist($tmpdata);

                                    if ($record_exist) {
                                        $tmpdata->id = $record_exist;

                                        $db->updateObject('#__kazisocial_invite', $tmpdata, 'id');
                                    } else {
                                        $db->insertObject('#__kazisocial_invite', $tmpdata);
                                    }
                                }
                            }
                        }
                        $msg = count($contacts) . ' Records Imported Successfully';
                    } else {
                        $msg = 'Import failed';
                    }
                    $app->redirect('index.php?option=com_kazisocial.invites', $msg);
                }
            }
            $msg = 'Yahoo Authentication Failed';
        }
        $app->redirect('index.php', $msg);
    }

}
