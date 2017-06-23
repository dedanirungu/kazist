<?php

/**
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Users\Components\Usergroups\Controllers;

defined('KAZIST') or exit('Not Kazist Framework');

use Joomla\Input\Input;
use Joomla\Log\Log;
use Accounts\Components\Journal\Models\JournalModel;
use Kazicode\Controller\KazicodeController;
use Kazicode\Service\Pdf\Kazipdf;
use Kazicode\Service\Media\MediaManager;
use Kazicode\Service\KaziFactory;
use Users\Components\Usergroups\Models\UsergroupsModel;

/**
 * Dashboard Controller class for the Application
 *
 * @since  1.0
 */
class UsergroupsController extends KazicodeController {

    /**
     * Save functions
     *
     * @return  void
     *
     * @since   1.0
     * @throws  \RuntimeException
     */
    public function save() {
        $kazifactory = new KaziFactory();
        

        $form = $this->request->request->get('form');

        try {
            $usergroupModel = new UsergroupsModel($input, $this->getContainer()->get('db'));
            $form = $usergroupModel->processRightTree($form);
            parent::save($form);
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf('Error: ' . $e->getMessage()));
        }
    }

}
