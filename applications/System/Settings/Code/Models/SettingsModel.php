<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace System\Settings\Code\Models;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\Model\BaseModel;
use Kazist\KazistFactory;
use Kazist\Service\Database\Query;

/**
 * Description of AdvertModel
 *
 * @author sbc
 */
class SettingsModel extends BaseModel {

    public function getSettings($alias) {

        $settings_arr = array();

        $dir = new \DirectoryIterator(JPATH_ROOT . 'applications');
        foreach ($dir as $fileinfo) {
            $file_name = $fileinfo->getFilename();
            if ($fileinfo->isDir() && !$fileinfo->isDot()) {

                $app_path = JPATH_ROOT . 'applications/' . $file_name;

                if (file_exists($app_path . '/setting.json')) {

                    $setting = json_decode(file_get_contents($app_path . '/setting.json'), true);

                    if ($alias == $setting['alias']) {
                        $setting = $this->prepareSetting($setting);
                    }

                    $settings_arr[$setting['alias']] = $setting;
                }
            }
        }

        ksort($settings_arr);

        return $settings_arr;
    }

    public function prepareSetting($setting_obj) {

        if (!empty($setting_obj)) {
            foreach ($setting_obj['groups'] as $group_key => $group) {
                if (!empty($group['settings'])) {
                    foreach ($group['settings'] as $seting_key => $setting) {

                        $setting_obj['groups'][$group_key]['settings'][$seting_key]['options'] = array();

                        if ($setting['source']['custom'] <> '') {
                            $setting_obj['groups'][$group_key]['settings'][$seting_key]['options'] = $setting['source']['custom'];
                        }

                        if ($setting['source'] <> '') {
                            $setting_obj['groups'][$group_key]['settings'][$seting_key]['options'] = array_merge($setting_obj['groups'][$group_key]['settings'][$seting_key]['options'], $this->getSettingOptions($setting));
                        }

                        $setting_obj['groups'][$group_key]['settings'][$seting_key]['default'] = $this->getSettingDefault($setting);
                    }
                }
            }
        }


        return $setting_obj;
    }

    public function getSettingDefault($setting) {

        $name = $setting['name'];

        $record = $this->getQueryedRecord('#__system_settings', 'ss', array('ss.name=:name'), array('name' => $name));

        if (is_object($record)) {
            return $record->value;
        } else {
            return $setting['default'];
        }
    }

    public function getSettingOptions($setting) {

        $tmp_array = array();
        $query = new Query();

        $table = $setting['source']['table']['name'];
        $wheres = $setting['source']['table']['where'];
        $orders = $setting['source']['table']['order'];
        $value = $setting['source']['table']['value'];
        $text = $setting['source']['table']['text'];

        if (is_array($setting) && $table <> '') {

            $query->select($text . ', ' . $value);
            $query->from($table);

            foreach ($wheres as $key => $where) {
                (!$key) ? $query->where($where) : $query->andWhere($where);
            }

            foreach ($orders as $key => $order) {
                $order_arr = explode(' ', $order);
                (!$key) ? $query->orderBy($order_arr[0], $order_arr[1]) : $query->addOrderBy($order_arr[0], $order_arr[1]);
            }

            $records = $query->loadObjectList();

            foreach ($records as $key => $record) {
                $tmp_array[] = array('value' => $record->$value, 'text' => $record->$text);
            }
        }

        return $tmp_array;
    }

    public function save($form) {


        foreach ($form as $key => $item) {

            $record = $this->getQueryedRecord('#__system_settings', 'ss', array('ss.name=:name'), array('name' => $key));

            if (is_object($record)) {
                $record->value = $item;
            } else {
                $record = new \stdClass();
                $record->name = $key;
                $record->value = $item;
            }

            $record->is_modified = 1;

            $this->saveRecord('#__system_settings', $record);
        }
    }

}
