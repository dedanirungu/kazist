<?php

/*
 * This file is part of Kazist Framework.
 * (c) Dedan Irungu <irungudedan@gmail.com>
 *  For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 * 
 */

/**
 * Description of SettingsController
 *
 * @author sbc
 */

namespace System\Settings\Code\Controllers\Admin;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\Controller\BaseController;

class SettingsController extends BaseController {

    public function indexAction($offset = 0, $limit = 10) {

        $tmp_alias = $this->request->get('alias');

        $alias_arr = explode('.', $tmp_alias);
        $alias = $alias_arr[0];

        if ($alias == '') {
            $alias = 'system';
        }

        $settings = $this->model->getSettings($alias);

        $tmp_data_arr['settings'] = $settings[$alias];
        $tmp_data_arr['alias'] = alias;

        $html = $this->render('System;Settings;Code;views:admin:setting.index.twig', $tmp_data_arr);

        $this->data_arr['settings'] = $settings;
        $this->data_arr['settings_html'] = $html;
        $this->data_arr['alias'] = $alias;
        $this->data_arr['return_url'] = base64_encode($this->model->generateUrl('admin.system.settings', array('alias' => $alias)));

        return parent::indexAction($offset, $limit);
    }

}
