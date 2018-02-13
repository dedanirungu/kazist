<?php

/**
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Users\Users\Code\Models;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\Model\BaseModel;
use Kazist\KazistFactory;
use Kazist\Service\Session\Session;
use Kazist\Service\System\System;
use Kazist\Service\User\User;
use Kazist\Service\Email\Email;
use Kazist\Service\User\Registration;
use Kazist\Service\User\Login;
use Kazist\Service\Database\Query;

/**
 * Model to get data for the issue list view
 *
 * @since  1.0
 */
class UsersModel extends BaseModel {

    public function appendSearchQuery($query) {

        $document = $this->container->get('document');
        $view = $this->request->get('view');
        $type = $this->request->get('type');
        $user_id = $this->request->get('user_id');

        $search = $document->search;
        $email = $search['email'];
        $username = $search['username'];

        $createdrange_arr = explode(' - ', $search['createdrange']);

        if ($search['createdrange'] <> '' && !empty($createdrange_arr)) {
            $search['date_created']['start'] = date('Y-m-d 00:00:00', strtotime($createdrange_arr[0]));
            $search['date_created']['end'] = date('Y-m-d 23:59:59', strtotime($createdrange_arr[1]));
        }

        parent::appendSearchQuery($query);

        if ($email <> '' || $username <> '') {
            $query->where('uu.email= :email OR uu.username= :username');
            $query->setParameter('email', $email);
            $query->setParameter('username', $username);
        }

        return $query;
    }

    /**
     * Save Function
     *
     * @return  boolean
     *
     * @since   1.0
     * @throws  \RuntimeException
     */
    public function save($form = '') {

        $is_valid = '';

        $factory = new KazistFactory();
        $registration = new Registration();

        $form = ($form <> '') ? $form : $this->request->get('form');

        if (isset($form['password']) && $form['password'] == '') {
            unset($form['password']);
        }

        $form_obj = json_decode(json_encode($form));
        $form_obj->group_id = (array) $form_obj->group_id;

        if ($form['id']) {
            $registration->user_id = $form['id'];
            $is_valid = $registration->validateRegistration($form_obj, false);

            if ($is_valid) {
                $user_id = $registration->registerUser($form_obj, $is_valid);
            }
        } else {
            $is_valid = $registration->validateRegistration($form_obj, true);
            $user_id = $registration->registerUser($form_obj, $is_valid);
        }

        $data_obj = new \stdClass();
        $data_obj->user_id = $user_id;
        $where_arr = array('user_id = :user_id');
        $factory->deleteRecords('#__users_users_groups', $where_arr, $data_obj);

        foreach ($form_obj->group_id as $key => $group_id) {

            $data_obj = new \stdClass();

            $data_obj->user_id = $user_id;
            $data_obj->group_id = $group_id;

            $where_arr = array('user_id = :user_id', 'group_id = :group_id');

            $factory->saveRecord('#__users_users_groups', $data_obj, $where_arr, $data_obj);
        }

        return $user_id;
    }

    public function loginUser() {

        $factory = new KazistFactory;
        $login = new Login();

        $has_logined_in = $login->loginUser();

        if (!$has_logined_in) {
            return $this->generateUrl('login');
        }
    }

    public function registerUser($form) {

        $user_id = 0;
        $user_obj = new \stdClass();

        $factory = new KazistFactory();
        $registration = new Registration();
        $user = $factory->getUser();

        $user_obj->name = $form['name'];
        $user_obj->username = $form['username'];
        $user_obj->email = $form['email'];
        $user_obj->password = $form['password'];
        $user_obj->password_again = $form['password_again'];
        $user_obj->country_id = $form['country_id'];
        $user_obj->location_id = $form['location_id'];
        $user_obj->phone = $form['phone'];
        $user_obj->address = $form['address'];

        $is_valid = $registration->validateRegistration($user_obj);

        if ($user->id) {
            $user_obj->id = $user->id;
        }

        if ($is_valid) {
            $user_id = $registration->registerUser($user_obj);
        }

        return $user_id;
    }

    /**
     * Delete Function
     *
     * @param   $id
     *
     * @return  boolean
     *
     * @since   1.0
     * @throws  \RuntimeException
     */
    public function logoutUser() {
        $session = new Session();
        $session->clear('user');
        $session->clear('user_id');
    }

    public function getUserByUsernameNPassword($username, $password) {
        $factory = new KazistFactory;
        $db = $factory->getDatabase();


        $query = new Query();

        $query->select('u.*,mm.file as media_file');
        $query->where('u.username=:username');
        $query->andWhere('u.password=:password');
        $query->setParameter('username', $username);
        $query->setParameter('password', $password);
        $query->from('#__users_users', 'u');
        $query->leftJoin('u', '#__media_media', 'mm', ' mm.id = u.avatar');

        $user = $query->loadObject();

        return $user;
    }

    public function getApplicationsTree() {
        $system = new System;
        $applications = $system->getApplicationsTree();

        return $applications;
    }

    public function accountVerification($verification) {
        $db = $this->getDb();
        $factory = new KazistFactory();

        $query = new Query();

        $query->select('u.*');
        $query->from('#__users_users', 'u');
        $query->where('verification=:verification');
        $query->setParameter('verification', $verification);
        $user = $query->loadObject();
// print_r((string)$query); exit;

        if (is_object($user)) {

            $user_obj = new \stdClass();
            $user_obj->id = $user->id;
            $user_obj->is_verified = 1;
            $user_obj->published = 1;
            if ($user_obj->id) {
                $factory->saveRecord('#__users_users', $user_obj);
            }
            return true;
        } else {
            return false;
        }
    }

    public function fetchFollow() {
        $tmparray = array();
        $tmpobject = new \stdClass();

        $session = new Session;
        $factory = new KazistFactory();


        $user = $session->get('user');

        $tmpobject->subset_id = $this->request->request->get('subset_id');
        $tmpobject->record_id = $this->request->request->get('record_id');
        $tmpobject->user_id = $user->id;

        $records = $this->getAllFollows($tmpobject->subset_id, $tmpobject->record_id);
        $id = $this->getFollowRecord($tmpobject);

        if (!empty($records)) {
            $tmparray['successful'] = true;
            $tmparray['users'] = $records;
        } else {
            $tmparray['successful'] = false;
        }

        $tmparray['follow_text'] = (!$id) ? 'User' : 'Remove User';
        $tmparray['has_right'] = (!$id) ? false : true;

        return json_encode($tmparray);
    }

    public function getUsersList() {

        $tmpobject = new \stdClass;
        $factory = new KazistFactory();


//$db = $this->getDb();

        $limit = 8;
        $offset = $this->request->request->get('offset', 0);

        $query = new Query();
        $query->select('COUNT(*)');
        $query = $this->prepareUsersQuery($query);
        $total = $query->loadResult();
        $query->clear();

        $query = new Query();
        $query->select('uu.*, mm.title as media_title, mm.file as media_file');
        $query = $this->prepareUsersQuery($query);

        $query->setFirstResult($offset);
        $query->setMaxResults($limit);

        $records = $query->loadObjectList();

        $tmpobject->records = $records;
        $tmpobject->successful = (!empty($records)) ? true : false;
        $tmpobject->offset = $offset;
        $tmpobject->limit = $limit;
        $tmpobject->total = $total;

        return json_encode($tmpobject);
    }

    public function prepareUsersQuery($query) {
        $db = $this->getDb();
        $factory = new KazistFactory();


        $user_ids = $this->request->request->get('user_ids', null, null);

        $query->from('#__users_users', 'uu');
        $query->leftjoin('uu', '#__media_media', 'mm', '  uu.avatar=mm.id');
        if (!empty($user_ids)) {
            $query->where(' uu.id NOT IN(' . implode(',', $user_ids) . ')');
        }
//print_r((string)$query); exit;
        return $query;
    }

    public function resendVerificationCode($form) {

        $factory = new KazistFactory();
        $registration = new Registration();

        $return_url = $this->generateUrl('login');

        $user = $this->getUserByEmail($form['email']);

        if (is_object($user)) {

            $registration->sendVerificationEmail($user);

            $msg = 'Your Account is now reset. Open your email and click reset Link.';
            $factory->enqueueMessage($msg, 'success');
            $return_url = $this->generateUrl('login');
        } else {

            $msg = 'Reseting Your Account Failed. User Account does not exist.';
            $factory->enqueueMessage($msg, 'error');
            $return_url = $this->generateUrl('forgot.password');
        }

        return $return_url;
    }

    public function changeUser($form) {

        $email = new Email();
        $login = new Login();
        $factory = new KazistFactory();
        $user = $factory->getUser();

        if ($form['new_password'] != $form['new_password_again']) {
            $msg = 'You New Password did not Match with Confirm New Password. ';
            $factory->enqueueMessage($msg, 'error');
            $return_url = $this->generateUrl('change.password');
        } elseif (is_object($user)) {

            $is_valid = $login->loginUser($user->username, $form['current_password']);

            if ($is_valid) {

                $user_obj = new \stdClass();
                $user_obj->id = $user->id;
                $user_obj->password = md5($form['new_password']);
                if ($user_obj->id) {
                    $factory->saveRecord('#__users_users', $user_obj);
                }
                $msg = 'Your Password Was Change Successfully.';
                $factory->enqueueMessage($msg, 'info');
                $return_url = $this->generateUrl('users.users.edit');
            } else {
                $msg = 'The password that in "Current Password" is Wrong. ';
                $factory->enqueueMessage($msg, 'error');
                $return_url = $this->generateUrl('change.password');
            }
        } else {
            $msg = 'You are currently logged out. Kindly Login again to Change You Password. ';
            $factory->enqueueMessage($msg, 'error');
            $return_url = $this->generateUrl('login');
        }

        return $return_url;
    }

    public function forgotUser($form) {

        $factory = new KazistFactory();
        $email = new Email();
        $return_url = $this->generateUrl('login');

        $user = $this->getUserByEmail($form['email']);

        if (!is_object($user)) {
            $msg = 'Your account is Does Not Exist.  Please Register.';
            $factory->enqueueMessage($msg, 'error');
            $return_url = $this->generateUrl('users.users.edit');
        } elseif (!$user->is_verified) {

            $msg = 'Your account is not yet verified. Please Verify your Account First or resend Verification Code.';
            $factory->enqueueMessage($msg, 'error');
            $return_url = $this->generateUrl('resend-verification');
        } elseif (is_object($user)) {

            $password = substr(md5(rand(0, 1000000)), 0, 7);

            $user->password_raw = $password;
            $user->password = md5($password);
            if ($user->id) {
                $factory->saveRecord('#__users_users', $user);
            }
            $user_arr = (array) $user;
            $user_arr['login_url'] = $this->generateUrl('login');
            $email->sendDefinedLayoutEmail('users.users.forgot', $form['email'], $user_arr);

            $msg = 'Your Account is now reset. Open your email and click reset Link';
            $factory->enqueueMessage($msg, 'success');
            $return_url = $this->generateUrl('login');
        } else {

            $msg = 'Reseting Your Account Failed. ';
            $factory->enqueueMessage($msg, 'error');
            $return_url = $this->generateUrl('forgot.password');
        }

        return $return_url;
    }

    public function getUserByEmail($email) {

        $factory = new KazistFactory;
        $db = $factory->getDatabase();


        $query = new Query();

        $query->select('u.*');
        $query->where('u.email=:email');
        $query->setParameter('email', $email);
        $query->from('#__users_users', 'u');

        $user = $query->loadObject();

        return $user;
    }

    public function fetchCaptcha() {

        $session = $this->container->get('session');
// Adapted for The Art of Web: www.the-art-of-web.com
// Please acknowledge use of this code by including this header.
// initialise image with dimensions of 120 x 30 pixels

        $image = @imagecreatetruecolor(200, 50) or die("Cannot Initialize new GD image stream");

// set background to white and allocate drawing colours
        $background = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
        imagefill($image, 0, 0, $background);
        $linecolor = imagecolorallocate($image, 0xCC, 0xCC, 0xCC);
        $textcolor = imagecolorallocate($image, 0x33, 0x33, 0x33);

// draw random lines on canvas
        for ($i = 0; $i < 6; $i++) {
            imagesetthickness($image, rand(1, 3));
            imageline($image, 0, rand(0, 30), 250, rand(0, 30), $linecolor);
        }


// add random digits to canvas
        $digit = '';
        for ($x = 15; $x <= 120; $x += 20) {
            $digit .= ($num = rand(0, 9));
            imagechar($image, rand(4, 5), $x, rand(2, 14), $num, $textcolor);
        }

// record digits in session variable
        $session->set('captcha-digit', $digit);

// display image and clean up
        header('Content-type: image/png');
        imagepng($image);
        imagedestroy($image);
        exit;
    }

    public function fetchUser() {

        $factory = new KazistFactory;

        $keyword = $this->request->get('keyword');

        $query = new Query();
        $query->select('u.id, u.username, u.name, u.phone, u.email');
        $query->from('#__users_users', 'u');

        if ($keyword <> '') {
            $query->where('u.email = :keyword');
            $query->orWhere('u.username = :keyword');
            $query->orWhere('u.phone = :keyword');
            $query->orWhere('u.name = :keyword');
            $query->setParameter('keyword', $keyword);
        } else {
            $query->where('1=-1');
        }
        $user = $query->loadObject();

        unset($user->password);

        return $user;
    }

    public function updateYesNo() {

        $factory = new KazistFactory();


        $item_id = $this->request->request->get('item_id');
        $item_status = $this->request->request->get('item_status');
        $item_field = $this->request->request->get('item_field');

        if ($item_field == 'is_verified') {

            $user = $factory->getRecord('#__users_users', 'uu', array('id=:id'), array('id' => $item_id));

            if (!$user->is_verified) {
                $this->accountVerification($user->verification);
            }

            $data_obj = new \stdClass();
            $data_obj->id = $item_id;
            $data_obj->published = ($item_status) ? 0 : 1;
            if ($data_obj->id) {
                $factory->saveRecord('#__users_users', $data_obj);
            }
        } else {
            parent::updateYesNo();
        }
    }

}
