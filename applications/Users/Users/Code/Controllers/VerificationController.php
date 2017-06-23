<?php

/**
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Users\Components\Users\Controllers;

defined('KAZIST') or exit('Not Kazist Framework');

use Users\Components\Users\Models\UsersModel;
use Kazicode\Controller\KazicodeController;
use Kazicode\Service\KaziFactory;

/**
 * Dashboard Controller class for the Application
 *
 * @since  1.0
 */
class VerificationController extends KazicodeController {

    public function verification() {

        $kazifactory = new KaziFactory();
        

        $verification = $this->request->request->get('verification');


        if ($verification <> '') {

            $usersModel = new UsersModel();
            $verified = $usersModel->accountVerification($verification);
            
            if ($verified) {
                $msg = 'Account Verified.';
                $kazifactory->enqueueMessage($msg, 'error');
                $return_url = $kazifactory->getFriendlyUrl('app=users&com=users&subset=users&view=login');
            } else {
                $msg = 'Invalid Verification Code.';
                $kazifactory->enqueueMessage($msg, 'error');
                $return_url = $kazifactory->getFriendlyUrl('app=social&com=member&subset=member&view=edit');
            }
        } else {
            $msg = 'Invalid Url';
            $kazifactory->enqueueMessage($msg, 'error');
            $return_url = $kazifactory->getFriendlyUrl('app=social&com=member&subset=member&view=edit');
        }

        $this->getApplication()->redirect($return_url);
    }

}
