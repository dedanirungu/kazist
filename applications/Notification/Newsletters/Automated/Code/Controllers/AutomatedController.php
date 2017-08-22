<?php

/*
 * This file is part of Kazist Framework.
 * (c) Dedan Irungu <irungudedan@gmail.com>
 *  For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 * 
 */

/**
 * Description of AutomatedController
 *
 * @author sbc
 */

namespace Notification\Newsletters\Automated\Code\Controllers;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\Controller\BaseController;
use Notification\Newsletters\Automated\Code\Models\AutomatedModel;

class AutomatedController extends BaseController {

    public function cronautonewsletterAction() {

        $this->model->sendAutomatedList();

        return $this->json($data);
    }

}
