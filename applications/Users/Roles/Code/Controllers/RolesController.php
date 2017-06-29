<?php

/*
 * This file is part of Kazist Framework.
 * (c) Dedan Irungu <irungudedan@gmail.com>
 *  For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 * 
 */

/**
 * Description of RolesController
 *
 * @author sbc
 */

namespace Users\Roles\Code\Controllers;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\Controller\BaseController;
use Users\Roles\Code\Models\RolesModel;

class RolesController extends BaseController {

    public function editAction() {

        $this->model = new RolesModel();

        $view_only = array(
            array('value' => 'view', 'text' => 'V')
        );

        $write_only = array(
            array('value' => 'write', 'text' => 'W')
        );

        $view_write = array(
            array('value' => 'view', 'text' => 'V'),
            array('value' => 'add', 'text' => 'A'),
            array('value' => 'write', 'text' => 'W'),
            array('value' => 'delete', 'text' => 'D'),
            array('value' => 'viewown', 'text' => 'VO'),
            array('value' => 'writeown', 'text' => 'WO'),
            array('value' => 'deleteown', 'text' => 'DO')
        );

        $applications = $this->model->getApplications();

        $item = $this->model->getRecord();
        $rights = json_decode($item->rights);
        $data_arr['rights'] = $rights;

        $data_arr['applications'] = $applications;
        $data_arr['view_only'] = $view_only;
        $data_arr['write_only'] = $write_only;
        $data_arr['view_write'] = $view_write;

        $this->html = $this->render('Users:Roles:Code:views:edit.index.twig', $data_arr);

        $response = $this->response($this->html);

        return $response;
    }

}
