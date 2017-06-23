<?php

/*
 * This file is part of Kazist Framework.
 * (c) Dedan Irungu <irungudedan@gmail.com>
 *  For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 * 
 */

/**
 * Description of UsersController
 *
 * @author sbc
 */

namespace Users\Users\Code\Controllers;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Users\Users\Code\Models\UsersModel;

class UsersController extends BaseController {

    public function editAction() {

        $this->model = new UsersModel();

        $item = $this->model->getRecord();
        $applications = $this->model->getApplicationsTree();

        $data_arr['item'] = $item;
        $data_arr['applications'] = $applications;

        $this->html = $this->render('Users:Users:Code:views:edit.index.twig', $data_arr);

        $response = $this->response($this->html);



        return $response;
    }

    public function fetchcaptchaAction() {
        $this->model->fetchCaptcha();
    }

}
