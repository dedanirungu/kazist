<?php

/**
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Users\Role\Ajax;

defined('KAZIST') or exit('Not Kazist Framework');

use Users\Role\Models\RoleModel;
use Kazist\KazistFactory;

/**
 * Dashboard Controller class for the Application
 *
 * @since  1.0
 */
class RoleAjax {

    /**
     * Save functions
     *
     * @return  void
     *
     * @since   1.0
     * @throws  \RuntimeException
     */
    public function ajaxloadapplications() {

        $factory = new KazistFactory();
        $factory->validUser();

        $roleModel = new RoleModel();
        echo $roleModel->loadApplicationComponents();
        exit;
    }

}
