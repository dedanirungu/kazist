<?php

/*
 * This file is part of Kazist Framework.
 * (c) Dedan Irungu <irungudedan@gmail.com>
 *  For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 * 
 */

/**
 * Description of HarvestersController
 *
 * @author sbc
 */

namespace Notification\Subscribers\Harvesters\Code\Controllers;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\Controller\BaseController;
use Notification\Subscribers\Harvesters\Code\Models\HarvestersModel;

class HarvestersController extends BaseController {

    public function emailharvesterAction() {

        $harvesterModel = new HarvestersModel();
        $harvesterModel->emailHarverster();

        return $this->json($data);
    }

}
