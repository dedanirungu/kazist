<?php

/*
 * This file is part of Kazist Framework.
 * (c) Dedan Irungu <irungudedan@gmail.com>
 *  For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 * 
 */

/**
 * Description of ExtensionsController
 *
 * @author sbc
 */

namespace System\Extensions\Code\Controllers\Admin;

defined('KAZIST') or exit('Not Kazist Framework');

use System\Extensions\Code\Models\ExtensionsModel;
use Kazist\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ExtensionsController extends BaseController {

    public function installAction($path, $type, $namespace) {
        $urls = array();

        $session = $this->container->get('session');
        $clear_name = str_replace('.', '_', $path);

        if ($path <> '' && $type == '') {
            $session_urls = $session->get('urls');

            $urls = $this->model->prepareUrl($path);
            $new_urls = (!is_array($session_urls)) ? $urls : array_merge($session_urls, $urls);
            $session->set('urls', $new_urls);
        } elseif ($path == '') {
            $urls = $this->model->prepareUrl();
            $session->set('urls', $urls);
        } else {
            $urls = $session->get('urls');
        }

        if ($clear_name == '') {
            $this->model->prepareTables($clear_name);
        }
        
        $this->model->install($path, $type, $namespace);

        if (!empty($urls)) {
            return $this->model->javascriptRedirector($path, $type, $namespace);
        }

        return $this->redirectToRoute('admin.system.extensions');
    }

    public function indexAction($offset = 0, $limit = 6) {

        $simple = $this->request->query->get('simple');

        $repositories = $this->model->getExtensionList();
        $data_arr['items'] = $repositories;

        $this->model->setAssets(JPATH_ROOT . '/assets/css/bootstrap.css');

        $this->html .= $this->render('System:Extensions:Code:views:admin:extension.list.twig', $data_arr);

        if ($simple <> '') {
            print_r($this->html);
            exit;
        }

        $response = $this->response($this->html);

        return $response;
    }

    public function updatesystemAction() {

        $this->model->updateSystem();

        return $this->redirectToRoute('admin.system.extensions');
    }

}
