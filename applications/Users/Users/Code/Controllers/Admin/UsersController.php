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

namespace Users\Users\Code\Controllers\Admin;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\KazistFactory;
use Kazist\Controller\BaseController;
use Users\Users\Code\Models\UsersModel;

class UsersController extends BaseController {

    public function saverightsAction() {

        $tmpclass = new \stdClass();
        $factory = new KazistFactory();
        $db = $factory->getDatabase();

        $this->model = new UsersModel();

        $form = $this->request->request->get('form');

        $return_url = (isset($form['return_url'])) ? $form['return_url'] : '';

        if ($return_url <> "") {
            //$return_url = base64_decode($return_url);
        } else {
            $return_url = WEB_ROOT;
        }

        $tmpclass->rights = json_encode($form['right']);
        $tmpclass->id = $form['subset_id'];

        $db->updateObject('#__system_subsets', $tmpclass, 'id');

        $this->redirect($return_url);
    }

    /**
     * Save functions
     *
     * @return  void
     *
     * @since   1.0
     * @throws  \RuntimeException
     */
    public function userslistAction() {
        $this->model = new UsersModel();
        $message = $this->model->getUsersList();
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
    public function saveuserAction() {
        $this->model = new UsersModel();
        $message = $this->model->saveUser();
        echo $message;
        exit;
    }

    public function fetchuserAction() {

        $this->model = new UsersModel();
        $message = $this->model->fetchUser();

        return $this->json($message);
    }

}
