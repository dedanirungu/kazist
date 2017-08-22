<?php

/*
 * This file is part of Kazist Framework.
 * (c) Dedan Irungu <irungudedan@gmail.com>
 *  For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 * 
 */

/**
 * Description of EmailsController
 *
 * @author sbc
 */

namespace Notification\Emails\Code\Controllers;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\Controller\BaseController;
use Kazist\Service\Email\EmailSender;
use Symfony\Component\HttpFoundation\JsonResponse;

class EmailsController extends BaseController {

    public function cronemailsenderAction() {


        $test = $this->request->get('test');

        if ($test <> '') {
            $factory = new \Kazist\KazistFactory();

            $factory->makeDir(JPATH_ROOT . 'uploads/cronemailsender');
        }

        $emailsender = new EmailSender($this->container, $this->request);

        $emailsender->sendEmailList();

        return $this->json('ok');
    }

}
