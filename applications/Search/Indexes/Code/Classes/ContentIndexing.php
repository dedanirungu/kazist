<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Search\Indexes\Code\Classes;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\Service\System\System;
use Kazist\KazistFactory;
use Kazist\StringModification;
use Kazist\Service\Database\Query;

/**
 * Description of ContentIndexing
 *
 * @author sbc
 */
class ContentIndexing {

    //put your code here

    public $subset_exist = false;

    public function processIndexing() {

        $subset_tables = $this->getSubsetTables();

        if (!empty($subset_tables)) {
            foreach ($subset_tables as $key => $subset_table) {

                $table_name = $subset_table->table_name;
                $table_name_arr = explode('_', $table_name);
                $table_name_arr = array_map('ucfirst', $table_name_arr);

                if (is_dir(JPATH_ROOT . 'applications/' . implode('/', $table_name_arr))) {
                    $this->indexSubsetTable($subset_table);
                }
            }
        }
    }

    public function indexSingleContent($table_name, $subset_content) {
        $subset_table = $this->getSingleSubsetTable($table_name);
        $this->saveContentIndex($subset_table, $subset_content);
    }

    public function deleteSingleContent($table_name, $subset_content) {
        $subset_table = $this->getSingleSubsetTable($table_name);
        $this->deleteContentIndex($subset_table, $subset_content);
    }

    public function deleteContentIndex($subset_table, $subset_content) {

        $data_obj = new \stdClass;
        $factory = new KazistFactory();

        $data_obj->subset_id = $subset_table->id;
        $data_obj->record_id = $subset_content->getId();

        $existing_obj = clone $data_obj;

        $where_arr = array();
        $where_arr[] = 'subset_id=:subset_id';
        $where_arr[] = 'record_id=:record_id';

        $factory->deleteRecords('#__search_indexes', $where_arr, $existing_obj);
    }

    public function saveContentIndex($subset_table, $subset_content) {

        $data_obj = new \stdClass;
        $factory = new KazistFactory();
        $string_modification = new StringModification();

        $data_obj->table_name = $subset_table->table_name;
        $data_obj->record_id = (method_exists($subset_content, 'getId')) ? $subset_content->getId() : $subset_content->id;

        $existing_obj = clone $data_obj;

        if ($subset_table->title_field <> '') {
            $title_func = 'get' . $string_modification->camelize($subset_table->title_field);
            $title_field = strtolower($subset_table->title_field);
            $data_obj->title = (method_exists($subset_content, $title_func)) ? $subset_content->$title_func() : $subset_content->$title_field;
        }

        if ($subset_table->content_field <> '') {
            $content_func = 'get' . $string_modification->camelize($subset_table->content_field);
            $content_field = strtolower($subset_table->content_field);
            $data_obj->description = (method_exists($subset_content, $title_func)) ? strip_tags($subset_content->$content_func()) : strip_tags($subset_content->$content_field);
        }

        if ($subset_table->image_field <> '') {
            $image_func = 'get' . $string_modification->camelize($subset_table->image_field);
            $image_field = strtolower($subset_table->image_field);
            $data_obj->image = (method_exists($subset_content, $image_func)) ? $subset_content->$image_func() : $subset_content->$image_field;
        }

        $data_obj->date_index = date('Y-m-d H:i:s');
        $data_obj->published = (method_exists($subset_content, 'getPublished')) ? $subset_content->getPublished() : $subset_content->published;
        $data_obj->date_created = (method_exists($subset_content, 'getDateCreated')) ? $subset_content->getDateCreated() : $subset_content->date_created;
        $data_obj->created_by = (method_exists($subset_content, 'getCreatedBy')) ? $subset_content->getCreatedBy() : $subset_content->created_by;
        $data_obj->modified_by = (method_exists($subset_content, 'getDateModified')) ? $subset_content->getDateModified() : $subset_content->date_modified;
        $data_obj->date_modified = (method_exists($subset_content, 'getModifiedBy')) ? $subset_content->getModifiedBy() : $subset_content->modified_by;
        $data_obj->is_processed = 1;

        $where_arr = array();
        $where_arr[] = 'si.table_name=:table_name';
        $where_arr[] = 'si.record_id=:record_id';

        $factory->saveRecord('#__search_indexes', $data_obj, $where_arr, $existing_obj);
    }

    public function getSubsetTables() {

        $factory = new KazistFactory();
        $db = $factory->getDatabase();

        $query = new Query();
        $query->select('ss.*');
        $query->from('#__search_subsets', 'ss');
        $query->where('ss.is_processed = 0 OR ss.is_processed IS NULL');
        $query->andWhere('ss.published = 1');
        $query->orderBy('ss.id ', 'DESC');

        $records = $query->loadObjectList();

        if (!count($records)) {
            $query = new Query();
            $query->update('#__search_subsets');
            $query->set('is_processed', '0');
            $query->execute();
        }

        return $records;
    }

    public function getSingleSubsetTable($table_name) {

        $factory = new KazistFactory();
        $db = $factory->getDatabase();

        $query = new Query();
        $query->select('ss.*');
        $query->from('#__search_subsets', 'ss');
        $query->where('ss.table_name = :table_name');
        $query->setParameter('table_name', $table_name);
        $query->orderBy('ss.id ', 'DESC');

        $records = $query->loadObject();

        return $records;
    }

    public function processSubsetContent($subset_table) {

        $subset_contents = $this->getSubsetContents($subset_table);
       
        if (!empty($subset_contents)) {
            foreach ($subset_contents as $key => $subset_content) {
                $this->saveContentIndex($subset_table, $subset_content);
            }
        }
    }

    public function getSubsetContents($subset_table) {

        $subquery = new Query();
        $subquery->select('record_id');
        $subquery->from('#__search_indexes', 'tt');
        $subquery->where('table_name=:table_name');

        $query = new Query();
        $query->select('tt.*');
        $query->from('#__' . $subset_table->table_name, 'tt');
        $query->where('id NOT IN (' . (string) $subquery . ')');
        $query->orWhere('tt.date_modified > :date_modified');
        $query->setParameter('date_modified', $subset_table->date_indexed);
        $query->setParameter('table_name', $subset_table->table_name);
        $query->orderBy('tt.id', 'DESC');

        $query->setFirstResult(0);
        $query->setMaxResults(10);

        $records = $query->loadObjectList();

        return $records;
    }

    //Old Code xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx


    public function indexSubsetTable($subset_table) {

        $factory = new KazistFactory();

        $this->processSubsetContent($subset_table);

        $subset_table->date_indexed = date('Y-m-d H:i:s');

        $factory->saveRecord('#__search_subsets', $subset_table);
    }

    public function getSubsetTable($app_id = '', $com_id = '', $subset_id = '') {

        $factory = new KazistFactory();
        $db = $factory->getDatabase();


        $new_app_id = ($app_id) ? $app_id : $this->request->request->get('app_id');
        $new_com_id = ($com_id) ? $com_id : $this->request->request->get('com_id');
        $new_subset_id = ($app_id) ? $subset_id : $this->request->request->get('subset_id');

        $query = new Query();
        $query->select('ss.*');
        $query->from('#__search_subsets', 'ss');
        $query->where('ss.app_id = :app_id');
        $query->andWhere('ss.com_id = :com_id');
        $query->andWhere('ss.subset_id = :subset_id');
        $query->setParameter('app_id', $new_app_id);
        $query->setParameter('com_id', $new_com_id);
        $query->setParameter('subset_id', $new_subset_id);
        $query->where('ss.published = 1');

        $query->orderBy('ss.id ', 'DESC');

        $records = $query->loadObject();

        return $records;
    }

}
