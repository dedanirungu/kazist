<?php

/**
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Users\Users\Ajax;

defined('KAZIST') or exit('Not Kazist Framework');

use Users\Users\Models\UsersModel;
use Kazist\KazistFactory;

/**
 * Dashboard Controller class for the Application
 *
 * @since  1.0
 */
class UsersAjax {

    /**
     * Save functions
     *
     * @return  void
     *
     * @since   1.0
     * @throws  \RuntimeException
     */
    public function userslist() {

        $userModel = new UsersModel();
        $message = $userModel->getUsersList();
        echo $message;
        exit;
    }

    /**
     * Save functions
     *
     * @return  void
     *
     * @since   1.0
     * @throws  \RuntimeException
     */
    public function saveuser() {

        $userModel = new UsersModel();
        $message = $userModel->saveUser();
        echo $message;
        exit;
    }

    public function fetchuser() {

        $userModel = new UsersModel();
        $user = $userModel->fetchUser();
        echo json_encode($user);
        exit;
    }

    public function fetchcaptcha() {

        $userModel = new UsersModel();
        $userModel->fetchCaptcha();
        exit;
    }

    public function updateyesno() {

        $factory = new KazistFactory();
        $userModel = new UsersModel();

        $factory->validUser();
        $userModel->updateYesNo();
    }

}
