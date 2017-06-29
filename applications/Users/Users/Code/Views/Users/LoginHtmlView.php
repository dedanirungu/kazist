<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Users\Users\Views\Users;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazicode\View\Edit\EditHtmlView as GeneralEditHtmlView;
use Kazicode\Service\KazistFactory;

/**
 * News HTML view class for the application
 *
 * @since  1.0
 */
class LoginHtmlView extends GeneralEditHtmlView {

    public function prepare() {
        parent::prepare();

        //$applications = $this->model->getApplicationsTree();
        // $this->renderer->set('applications', $applications);

        $this->renderer->set('show_title', 1);
        $this->renderer->set('show_cancel', 1);
    }

}
