<?php

/*
 * This file is part of Kazist Framework.
 * (c) Dedan Irungu <irungudedan@gmail.com>
 *  For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 * 
 */

/**
 * Description of SubscribersController
 *
 * @author sbc
 */

namespace Notification\Subscribers\Code\Controllers;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\Controller\BaseController;
use Kazist\KazistFactory;

class SubscribersController extends BaseController {

    function cronsyncuserAction() {

        $this->model->syncSyncUsers();
       
        return $this->json($data);
    }

    function indexAction($offset = 0, $limit = 0) {
        $factory = new KazistFactory();

        $factory->enqueueMessage('To Subscribe to this site use subscription form.');

        return $this->redirectToRoute('home');
    }

    function saveAction($form_data = '') {

        $factory = new KazistFactory();

        $form = $this->request->get('form');

        $subscriber = $factory->getRecord('#__notification_subscribers', 'ns', array('ns.email=:email'), array('email' => $form['email']));

        if (!is_object($subscriber)) {
            return parent::saveAction($form_data);
        } else {
            $factory->enqueueMessage('You are already subscribed.');
            return $this->redirectToRoute('home');
        }
    }

    function subscribeAction() {

        $subscriber = $this->model->unsubscribeUser();

        $data_arr['subscriber'] = $subscriber;

        $this->html = $this->render('Notification:Subscribers:Code:views:subscriber.index.twig', $data_arr);

        $response = $this->response($this->html);

        return $response;
    }

    function unsubscribeAction() {

        $subscriber = $this->model->unsubscribeUser();

        $data_arr['subscriber'] = $subscriber;

        $this->html = $this->render('Notification:Subscribers:Code:views:subscriber.index.twig', $data_arr);

        $response = $this->response($this->html);

        return $response;
    }

}
