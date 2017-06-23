<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Users\Addons\Users\Models;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\KazistFactory;
use Kazist\Service\Database\Query;

class UsersModel {

    public function getInfo() {
        return 'Hello World!';
    }

    public function getUserGroups() {

        $query = new Query();

        $query->select('uug.*');
        $query->from('#__users_groups', 'uug');

        $query->setFirstResult(0);
        $query->setMaxResults(10);

        $records = $query->loadObjectList();

        if (!empty($records)) {
            foreach ($records as $key => $record) {
                $records[$key]->total_member = $this->getUserGroupCount($record->id);
            }
        }

        return $records;
    }

    public function getUserGroupCount($group_id) {

        $query = new Query();

        $query->select('COUNT(*) AS total');
        $query->from('#__users_users_groups', 'uug');
        $query->where('uug.group_id=:group_id');
        $query->setParameter('group_id', $group_id);

        $record = $query->loadObject();

        return $record->total;
    }

    public function getUserGroupsCounter($usergroups) {

        $tmp_array = array();

        if (!empty($usergroups)) {
            foreach ($usergroups as $usergroup) {

                $group_name = substr($usergroup->name, 0, 10);
                $tmp_array[] = '["' . $group_name . '",' . $usergroup->total_member . ']';
            }
        }

        $tmp_array = '[' . implode(',', $tmp_array) . ']';

        return $tmp_array;
    }

}
