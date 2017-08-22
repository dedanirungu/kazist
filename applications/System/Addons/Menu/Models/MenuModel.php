<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace System\Addons\Menu\Models;

use Kazist\KazistFactory;
use Kazist\Service\Database\Query;

class MenuModel {

    public $app;
    public $container;
    public $block_id;
    public $paths = array();

    public function getInfo() {
        return 'Hello World!';
    }

    public function getCategoriesByAlias($alias = '', $fetch_menu = false, $is_core = true) {
        return $this->getCategories(null, $alias, $fetch_menu, $is_core);
    }

    public function getCategoriesById($category_id = '', $fetch_menu = false, $is_core = false) {
        return $this->getCategories($category_id, '', $fetch_menu, $is_core);
    }

    public function getCategories($category_id = '', $alias = '', $fetch_menu = false, $is_core = false) {

        $query = new Query();

        $query->select('smc.*');
        $query->from('#__system_menus_categories', 'smc');
        $query->where('smc.published=1');
        $query->orderBy('smc.title');

        if ($is_core) {
            $query->andWhere('smc.is_core=:is_core');
            $query->setParameter('is_core', $is_core);
        }

        if ($category_id) {
            $query->andWhere('smc.id=:id');
            $query->setParameter('id', $category_id);
        }

        if ($alias != '') {
            $query->andWhere('smc.alias=:alias');
            $query->setParameter('alias', $alias);
        }

        $records = $query->loadObjectList();

        if (!empty($records) && $fetch_menu) {
            foreach ($records as $key => $record) {
                $records[$key]->menus = $this->getCategoriesMenus($record->id);
            }
        }

        return $records;
    }

    public function getCategoriesMenus($category_id) {


        $factory = new KazistFactory;

        $query = new Query();

        $query->select('sm.*');
        $query->from('#__system_menus', 'sm');
        $query->where('sm.category_id=:category_id');
        $query->andWhere('sm.published=1');
        $query->orderBy('sm.parent_id');
        $query->addOrderBy('-sm.ordering', 'DESC');
        $query->addOrderBy('sm.title');
        $query->setParameter('category_id', (int) $category_id);

        $records = $query->loadObjectList();

        $records = $this->processTree($records);

        return $records;
    }

    public function processTree($records) {

        $tree_arr = array();

        if (!empty($records)) {
            foreach ($records as $key => $record) {

                $parent_id = $record->parent_id;


                if ($parent_id) {
                    $tree_arr[$parent_id]->children[] = $record;
                } else {
                    $tree_arr[$record->id] = $record;
                }
            }
        }

        return $tree_arr;
    }

    public function loadMenuFromFiles() {


        $active_menu = '';
        $menu_arr = array();
        $child_menu_arr = array();
        $document = $this->container->get('document');
        $extension_path = $document->extension_path;
        $extension_path_arr = explode('/', $extension_path);
        $current_app = strtolower($extension_path_arr[0]);

        $dir = new \DirectoryIterator(JPATH_ROOT . 'applications');

        foreach ($dir as $fileinfo) {
            $file_name = $fileinfo->getFilename();
            if ($fileinfo->isDir() && !$fileinfo->isDot()) {

                $app_path = JPATH_ROOT . 'applications/' . $file_name;
                $listed_app = strtolower($file_name);

                if (file_exists($app_path . '/menu.json')) {

                    $menu_obj = json_decode(file_get_contents($app_path . '/menu.json'));

                    if (isset($menu_obj->parent) && $menu_obj->parent <> '') {
                        $child_menu_arr[] = $menu_obj;
                    } else {
                        $firstCharacter = substr($menu_obj->title, 0, 1);
                        $menu_obj->active = ($listed_app === $current_app) ? true : false;

                        if ($menu_obj->active) {
                            $active_menu = $menu_obj->alias;
                        }

                        $menu_arr[$menu_obj->alias] = $menu_obj;
                    }
                }
            }
        }

        foreach ($child_menu_arr as $child_menu) {
            if (array_key_exists($child_menu->parent, $menu_arr)) {

                $sub_menus = $child_menu->menulist;

                foreach ($sub_menus as $sub_menu) {
                    $menu_arr[$child_menu->parent]->menulist[] = $sub_menu;
                }
            }
        }

        foreach ($menu_arr as $key => $menu) {
            if ($menu->active) {
                unset($menu_arr[$key]);
                $menu_arr['1first'] = $menu;
            }
        }


        ksort($menu_arr);

        return $menu_arr;
    }

}
