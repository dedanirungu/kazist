<?php

/**
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Users\Users\Groups\Code\Models;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\Model\BaseModel;
use Kazist\Service\System\System;

/**
 * Model to get data for the issue list view
 *
 * @since  1.0
 */
class UsergroupsModel extends BaseModel {

    public function getApplicationsTree() {
        $system = new System;
        $applications = $system->getApplicationsTree();

        return $applications;
    }

    public function processRightTree($form) {
        $tmpright = array();
        $right = $form['rights'];

        $applications = $this->getApplicationsTree();

        if (!empty($applications)) {
            foreach ($applications as $key => $application) {
                $tmpright['applications'][$application->name]['view'] = 0;

                if (isset($right['applications'][$application->name][0])) {
                    $tmpright['applications'][$application->name]['view'] = 1;
                }

                if (!empty($application->components)) {
                    foreach ($application->components as $key => $component) {
                        $tmpright['applications'][$application->name]['components'][$component->name]['view'] = 0;

                        if (isset($right['applications'][$application->name]['components'][$component->name][0])) {
                            $tmpright['applications'][$application->name]['components'][$component->name]['view'] = 1;
                        }

                        if (!empty($component->subsets)) {
                            foreach ($component->subsets as $key => $subset) {
                                $tmpright['applications'][$application->name]['components'][$component->name]['subsets'][$subset->name]['view'] = 0;
                                $tmpright['applications'][$application->name]['components'][$component->name]['subsets'][$subset->name]['write'] = 0;
                                $tmpright['applications'][$application->name]['components'][$component->name]['subsets'][$subset->name]['delete'] = 0;
                                $tmpright['applications'][$application->name]['components'][$component->name]['subsets'][$subset->name]['viewown'] = 0;
                                $tmpright['applications'][$application->name]['components'][$component->name]['subsets'][$subset->name]['writeown'] = 0;
                                $tmpright['applications'][$application->name]['components'][$component->name]['subsets'][$subset->name]['deleteown'] = 0;

                                if (isset($right['applications'][$application->name]['components'][$component->name]['subsets'][$subset->name][0])) {
                                    $tmpright['applications'][$application->name]['components'][$component->name]['subsets'][$subset->name]['view'] = 1;
                                }

                                if (isset($right['applications'][$application->name]['components'][$component->name]['subsets'][$subset->name][1])) {
                                    $tmpright['applications'][$application->name]['components'][$component->name]['subsets'][$subset->name]['write'] = 1;
                                }

                                if (isset($right['applications'][$application->name]['components'][$component->name]['subsets'][$subset->name][2])) {
                                    $tmpright['applications'][$application->name]['components'][$component->name]['subsets'][$subset->name]['delete'] = 1;
                                }
                                if (isset($right['applications'][$application->name]['components'][$component->name]['subsets'][$subset->name][3])) {
                                    $tmpright['applications'][$application->name]['components'][$component->name]['subsets'][$subset->name]['viewown'] = 1;
                                }
                                if (isset($right['applications'][$application->name]['components'][$component->name]['subsets'][$subset->name][4])) {
                                    $tmpright['applications'][$application->name]['components'][$component->name]['subsets'][$subset->name]['writeown'] = 1;
                                }
                                if (isset($right['applications'][$application->name]['components'][$component->name]['subsets'][$subset->name][5])) {
                                    $tmpright['applications'][$application->name]['components'][$component->name]['subsets'][$subset->name]['deleteown'] = 1;
                                }
                            }
                        }
                    }
                }
            }
        }

        $form['rights'] = json_encode($tmpright);


        return $form;
    }

}
