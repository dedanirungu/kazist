<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Users\Addons\Users\Controllers\Admin;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\Controller\AddonController;
use Users\Addons\Users\Models\UsersModel;

/**
 * Kazist view class for the application
 *
 * @since  1.0
 */
class UsersController extends AddonController {

    function userPieChartIndexAction() {

        $model = new UsersModel;

        $usergroups = $model->getUserGroups();
        $usergroups_json = $model->getUserGroupsCounter($usergroups);

        $data_arr['data'] = $usergroups_json;
        $data_arr['usergroups'] = $usergroups;

        $this->html .= $this->render('Kazist:views:chart:chart.show.pie.twig', $data_arr);
        $this->html .= $this->render('Users:Addons:Users:views:admin:users.charts.twig', $data_arr);

        $response = $this->response($this->html);


        return $response;
    }

}
