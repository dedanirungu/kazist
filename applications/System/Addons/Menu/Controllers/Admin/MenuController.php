<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace System\Addons\Menu\Controllers\Admin;

use Kazist\Controller\AddonController;
use System\Addons\Menu\Models\MenuModel;

/**
 * Kazist view class for the application
 *
 * @since  1.0
 */
class MenuController extends AddonController {

    public $flexview_id = '';

    function indexAction($alias = '') {

        $model = new MenuModel;

        $model->container = $this->container;
        $model->flexview_id = $this->flexview_id;

        $menus = $model->loadMenuFromFiles();

        $data_arr['menus'] = $menus;

        $this->html = $this->render('System:Addons:Menu:views:admin:menu.twig', $data_arr);

        $response = $this->response($this->html);



        return $response;
    }

    function quickmenuAction($offset = 0, $limit = 6) {

        $model = new MenuModel;

        $model->flexview_id = $this->flexview_id;

        $this->html = $this->render('System:Addons:Menu:views:admin:quickmenu.twig', array());

        $response = $this->response($this->html);

        return $response;
    }

    function iconmenuAction($offset = 0, $limit = 6) {
       
        $model = new MenuModel;

        $model->container = $this->container;
        $model->flexview_id = $this->flexview_id;

        $menus = $model->loadMenuFromFiles();

        $data_arr['categories'] = $menus;

        $this->html = $this->render('System:Addons:Menu:views:admin:iconmenu.twig', $data_arr);

        $response = $this->response($this->html);



        return $response;
    }

}
