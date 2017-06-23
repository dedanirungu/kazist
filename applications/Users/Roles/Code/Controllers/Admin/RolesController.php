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

namespace Users\Roles\Code\Controllers\Admin;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\Controller\BaseController;
use Users\Roles\Code\Models\RolesModel;

class RolesController extends BaseController {

    public function savepermissionAction() {

        $this->model->saveRoutePermission();

        echo 'true';

        exit;
    }

    public function routesAction() {

        $data_arr = array();

        $data_arr['routes'] = $this->model->getRouteList();
        $data_arr['roles'] = $this->model->getRolesList();
        $data_arr['role_id'] = $this->request->get('role_id');

        $html = $this->render('Users:Roles:Code:views:extension.routes.twig', $data_arr);

        echo $html;

        exit;
    }

}
