<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Users\Users\Groups\Views\Usergroups;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazicode\View\Edit\EditHtmlView as GeneralEditHtmlView;
use Kazicode\Service\KazistFactory;

/**
 * News HTML view class for the application
 *
 * @since  1.0
 */
class EditHtmlView extends GeneralEditHtmlView {

    public function prepare() {
        parent::prepare();

        $view_only = array(
            array('value' => 'view', 'text' => 'V')
        );

        $write_only = array(
            array('value' => 'write', 'text' => 'W')
        );

        $view_write = array(
            array('value' => 'view', 'text' => 'V'),
            array('value' => 'write', 'text' => 'W'),
            array('value' => 'delete', 'text' => 'D'),
            array('value' => 'viewown', 'text' => 'VO'),
            array('value' => 'writeown', 'text' => 'WO'),
            array('value' => 'deleteown', 'text' => 'DO')
        );

        $applications = $this->model->getApplicationsTree();


        $edit_index = $this->renderer->get('edit_index');
        $rights = json_decode($edit_index->rights);
        $this->renderer->set('rights', $rights);

        $this->renderer->set('applications', $applications);
        $this->renderer->set('view_only', $view_only);
        $this->renderer->set('write_only', $write_only);
        $this->renderer->set('view_write', $view_write);
    }

}
