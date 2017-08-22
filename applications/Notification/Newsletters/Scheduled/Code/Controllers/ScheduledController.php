<?php

/*
 * This file is part of Kazist Framework.
 * (c) Dedan Irungu <irungudedan@gmail.com>
 *  For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 * 
 */

/**
 * Description of ScheduledController
 *
 * @author sbc
 */

namespace Notification\Newsletters\Scheduled\Code\Controllers;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\Controller\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Notification\Newsletters\Scheduled\Models\ScheduledModel;

class ScheduledController extends BaseController {

    public function cronscheduledAction() {

        $this->model->sendScheduledList();

        return $this->json($data);
    }

    public function tableinputAction() {

        echo $this->model->getTableInputList();
        exit;
    }

    public function tablefieldinputAction() {

        $fields_list = $this->model->getTableFieldInputList();

        $response = new JsonResponse($fields_list);

        return $response;
    }

}
