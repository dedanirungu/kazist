<?php

/**
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Users\Roles\Code\Models;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\Model\BaseModel;
use Kazist\Service\System\System;
use Kazist\KazistFactory;
use Kazist\Service\Database\Query;

/**
 * Model to get data for the issue list view
 *
 * @since  1.0
 */
class RolesModel extends BaseModel {

    public function saveRoutePermission() {

        $role_id = $this->request->get('role_id');
        $permission = $this->request->get('permission');
        $route = $this->request->get('route');
        $value = $this->request->get('value');

        $data_obj = new \stdClass();
        $data_obj->role_id = $role_id;
        $data_obj->route = $route;
        $exist_obj = clone $data_obj;
        $data_obj->$permission = (int) $value;

        $id = $this->saveRecordByEntity('#__users_permission', $data_obj, array('role_id=:role_id', 'route_id=:route_id'), $exist_obj);
    }

    public function getViewSides() {
        $tmp_array = array();

        $tmp_array[] = array('value' => 'frontend', 'text' => 'Front Side');
        $tmp_array[] = array('value' => 'backend', 'text' => 'Back Side');

        return $tmp_array;
    }

    public function getSystemRouteSets() {

        $tmp_array = array();

        $dir = new \DirectoryIterator(JPATH_ROOT . 'applications');

        foreach ($dir as $fileinfo) {

            $file_name = $fileinfo->getFilename();

            if ($fileinfo->isDir() && !$fileinfo->isDot()) {

                $app_path = JPATH_ROOT . 'applications/' . $file_name;

                if (file_exists($app_path . '/namespace.json')) {
                    $namespace_arr = json_decode(file_get_contents($app_path . '/namespace.json'), true);

                    foreach ($namespace_arr as $key => $namespace) {

                        $code_folder = JPATH_ROOT . 'applications/' . str_replace('\\', '/', $namespace['namespace']);

                        if (file_exists($code_folder . '/Code/route.json')) {
                            
                            $namespace_key = str_replace('\\', '', $namespace['namespace']);
                            $namespace_label = str_replace('\\', ' ', $namespace['namespace']);
                            $tmp_array[$namespace_key] = array('value' => $namespace['namespace'], 'text' => $namespace_label);
                        }
                    }
                }
            }
        }

        ksort($tmp_array);

        return $tmp_array;
    }

    public function getRouteList() {

        $tmp_array = array();
        $tmp_role_id = $this->request->get('role_id');
        $tmp_route = $this->request->get('extension');
        $viewside = $this->request->get('viewside');

        $tmp_route_path = JPATH_ROOT . 'applications/' . $tmp_route . '/Code/route.json';
        $route_path = str_replace('\\', '/', $tmp_route_path);

        if (file_exists($route_path)) {

            $front_back_routes = json_decode(file_get_contents($route_path), true);

            $routes = $front_back_routes[$viewside];

            if (!empty($routes)) {
                foreach ($routes as $key => $route) {
                    $permission_str = str_replace(' ', '', $route['permissions']);
                    $routes[$key]['permissions'] = explode(',', $permission_str);
                    $routes[$key]['route_permissions'] = $this->getRoutePermissions($route, $tmp_role_id);
                }
            }
        }


        return $routes;
    }

    public function getRoutePermissions($route, $role_id) {

        $query = new Query();
        $query->select('srp.*');
        $query->from('#__users_permission', 'srp');
        $query->where('srp.route = :route');
        $query->setParameter('route', $route['unique_name']);
        $query->andWhere('srp.role_id = :role_id');
        $query->setParameter('role_id', $role_id);
        $record = $query->loadObject();

        return $record;
    }

    public function getRolesList($record) {

        $tmp_array = array();

        $query = new Query();
        $query->select('ur.*');
        $query->from('#__users_roles', 'ur');
        $query->where('ur.published = 1');
        $records = $query->loadObjectList();

        if (!empty($records)) {
            foreach ($records as $key => $record) {
                $tmp_array[$record->id] = $record;
            }
        }

        return $tmp_array;
    }

}
