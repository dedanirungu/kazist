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
        $route_id = $this->request->get('route_id');
        $value = $this->request->get('value');

        $data_obj = new \stdClass();
        $data_obj->role_id = $role_id;
        $data_obj->route_id = $route_id;
        $exist_obj = clone $data_obj;
        $data_obj->$permission = (int) $value;

        $id = $this->saveRecordByEntity('#__system_routes_permissions', $data_obj, array('role_id=:role_id', 'route_id=:route_id'), $exist_obj);
    }

    public function getViewSides() {
        $tmp_array = array();

        $tmp_array[] = array('value' => 'front', 'text' => 'Front Side');
        $tmp_array[] = array('value' => 'back', 'text' => 'Back Side');

        return $tmp_array;
    }

    public function getSystemExtensions() {

        $tmp_array = array();

        $query = new Query();
        $query->select('se.*');
        $query->from('#__system_extensions', 'se');
        $query->where('extension=:extension');
        $query->setParameter('extension', 'component');
        $query->orderBy('se.name');

        $records = $query->loadObjectList();

        if (!empty($records)) {
            foreach ($records as $record) {
                $tmp_array[] = array('value' => $record->name, 'text' => $record->name);
            }
        }

        return $tmp_array;
    }

    public function getRouteList() {

        $tmp_array = array();
        $tmp_route = $this->request->get('extension');
        $viewside = $this->request->get('viewside');
        $route = str_replace('kazist/', '', $tmp_route);

        $query = new Query();
        $query->select('sr.*');
        $query->from('#__system_routes', 'sr');
        if ($viewside == 'front') {
            $query->where('route LIKE :route_front');
            $query->setParameter('route_front', $route . '/%');
        } elseif ($viewside == 'back') {
            $query->orWhere('route LIKE :route_back');
            $query->setParameter('route_back', 'admin/' . $route . '/%');
        } else {
            $query->where('route LIKE :route_front');
            $query->setParameter('route_front', $route . '/%');
            $query->orWhere('route LIKE :route_back');
            $query->setParameter('route_back', 'admin/' . $route . '/%');
        }
        $query->orderBy('sr.route');
        $records = $query->loadObjectList();

        if (!empty($records)) {
            foreach ($records as $key => $record) {
                $permission_str = str_replace(' ', '', $records[$key]->permissions);
                $records[$key]->permissions = explode(',', $permission_str);
                $records[$key]->route_permissions = $this->getRoutePermissions($record);
            }
        }

        return $records;
    }

    public function getRoutePermissions($record) {

        $tmp_array = array();

        $query = new Query();
        $query->select('srp.*');
        $query->from('#__system_routes_permissions', 'srp');
        $query->where('srp.route_id LIKE :route_id');
        $query->setParameter('route_id', $record->id);
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
