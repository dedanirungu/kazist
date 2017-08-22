<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Media\Media\Code\Models;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\Controller\KazistController;
use Kazist\KazistFactory;
use Kazist\Model\BaseModel;
use Kazist\Service\Media\MediaManager;
use Kazist\Service\Database\Query;

/**
 * Dashboard Controller class for the Application
 *
 * @since  1.0
 */
class MediaModel extends BaseModel {

    //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx Update Media
    function updateMedia($uploads, $details) {

        $app = $details['application_name'];
        $com = $details['component_name'];
        $subset = $details['subset_name'];

        $factory = new KazistFactory();
        $media_ids = $factory->uploadMedia('media', null, null, $app, $com, $subset);

        $media_id = $media_ids[0];

        return $media_id;
    }

    function fileExist($file_location) {
        $factory = new KazistFactory;
        $db = $factory->getDatabase();

        $query = $db->getQuery();
        $query->select('mm.*');
        $query->where('mm.file=:file_location');
        $query->setParameter('file_location', $file_location);
        $query->from('#__media_media', 'mm');

        //print_r((string)$query); exit;
        $record = $query->loadObject();

        return (is_object($record)) ? $record->id : false;
    }

    function prepareDataForSaving($uploaddir, $details) {
        $tmpobject = new \stdClass();

        foreach ($details as $key => $detail) {
            switch ($key) {
                case 'application_name':
                    $application_id = $this->getApplication($detail);
                    $tmpobject->application_id = $application_id;
                    break;
                case 'component_name':
                    $component_id = $this->getComponent($detail);
                    $tmpobject->component_id = $component_id;
                    break;
                case 'subset_name':
                    $subset_id = $this->getSubset($detail);
                    $tmpobject->subset_id = $subset_id;
                    break;
                case 'name':
                    //Do nothing
                    break;
                default:
                    $tmpobject->$key = $detail;
                    break;
            }
        }

        $tmpobject->file = $uploaddir . $details['name'];

        return $tmpobject;
    }

    function updateDB($data_object, $media_id) {

        $factory = new KazistFactory;
        $db = $factory->getDatabase();

        if ($media_id) {
            $data_object->id = $media_id;
            $db->updateObject('#__media_media', $data_object, 'id');
        } else {
            $db->insertObject('#__media_media', $data_object);
            $media_id = $db->insertid();
        }

        return $media_id;
    }

    function uploadFile($uploaddir, $uploads) {

        if (move_uploaded_file($uploads['tmp_name'], JPATH_ROOT . $uploaddir . basename($uploads['name']))) {
            $files[] = $uploaddir . $uploads['name'];
        } else {
            $error = true;
        }
    }

    function getUploadDir($details) {
        $uploaddir = 'uploads/';

        if ($details['application_name'] <> '') {
            $uploaddir = $uploaddir . $details['application_name'] . '/';
        }

        if ($details['component_name'] <> '') {
            $uploaddir = $uploaddir . $details['component_name'] . '/';
        }

        if ($details['subset_name'] <> '') {
            $uploaddir = $uploaddir . $details['subset_name'] . '/';
        }

        if (!is_dir($uploaddir)) {
            $oldmask = umask(0);
            mkdir(JPATH_ROOT . '/' . $uploaddir, 0777, true);
            umask($oldmask);
        }

        return $uploaddir;
    }

    //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx Load Media List
    function getMediaList($media_ids = '', $media_not_ids = '', $offset = 0) {
        //print_r($offset); exit;

        $tmparray = array();
        $limit = 20;

        $mediamanager = new MediaManager();
        $factory = new KazistFactory;
        $db = $factory->getDatabase();

        $session = $factory->getSession();

        $session_keyword = $session->get('media_keyword');
        $new_keyword = $this->request->get('keyword');

        if ($new_keyword <> $session_keyword) {
            $offset = 0;
            $session->set('media_keyword', $new_keyword);
        }

        $selectquery = $this->getSelectQuery($db, $media_ids, $media_not_ids);
        $selectquery->setFirstResult($offset);
        $selectquery->setMaxResults($limit);
        $records = $selectquery->loadObjectList();

        $selectquery = $this->getSelectQuery($db, $media_ids, $media_not_ids, TRUE);
        $total = $selectquery->loadResult();

        if (!empty($selectquery)) {
            foreach ($records as $key => $record) {
                $file_name = $mediamanager->getFileName($record->file);

                $tmpobject = new \stdClass();
                $tmpobject->id = $record->id;
                $tmpobject->title = $record->title;
                $tmpobject->image = $mediamanager->getFileImage($record->file, $record->extension);

                if ($tmpobject->title == '') {
                    $tmpobject->title = $file_name;
                }
                $tmparray[] = $tmpobject;
            }
        }
        //print_r($tmparray); exit;

        return array($tmparray, $offset, $limit, $total);
    }

    function getSelectQuery($db, $media_ids, $media_not_ids, $istotal = false) {

        $factory = new KazistFactory();


        $keyword = $this->request->get('keyword');
        $type = $this->request->get('type');
        $error = $this->request->get('error');

        if (!is_array($media_ids) && $media_ids <> '') {
            $media_ids = (array) $media_ids;
        }

        if (!is_array($media_not_ids) && $media_not_ids <> '') {
            $media_not_ids = (array) $media_not_ids;
        }

        $query = new Query();

        if ($istotal) {
            $query->select(' COUNT(*) as total ');
        } else {
            $query->select(' mm.* ');
        }

        if ($type) {
            $query->where('type = :type');
            $query->setParameter('type', $type);
        }

        if (!empty($media_ids)) {
            $media_ids = implode(',', $media_ids);
            $query->andWhere('mm.id IN (' . $media_ids . ')');
        }

        if (!empty($media_not_ids)) {
            $media_not_ids = implode(',', $media_not_ids);
            $query->andWhere('mm.id NOT IN (' . $media_not_ids . ')');
        }

        if ($keyword != '') {
            $temp_keyword = '%' . $keyword . '%';
            $query->andWhere('(title LIKE :temp_keyword OR  file LIKE  :temp_keyword )');
            $query->setParameter('temp_keyword', $temp_keyword);
        }

        $query->from('#__media_media', 'mm');
        $query->orderBy('id', 'DESC');

        return $query;
    }

    function appendAdditionalDetail($item) {
        $factory = new KazistFactory;


        $mediamanager = new MediaManager();
        $item->show_iframe = false;
        $item->show_iframe = false;
        $item->return_url = base64_decode($this->request->request->get('return_url'));

        $file_extension = $mediamanager->getFileExtension($item->file);

        if ($mediamanager->odf_ext == 'odf') {
            $item->show_iframe = true;
        }
        if ($mediamanager->odf_ext == 'image') {
            $item->show_image = true;
        }
        // print_r($item); exit;
        return $item;
    }

    //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx  analyseMedia
    function analyseMedia() {

        $factory = new KazistFactory;
        $db = $factory->getDatabase();
        $mediamanager = new MediaManager();

        $medias = $this->getMedias();

        if (!empty($medias)) {
            foreach ($medias as $media) {

                $not_found = $this->checkFileExist($media);

                $data_obj = new \stdClass();
                $data_obj->id = $media->id;
                $data_obj->title = ($media->title != '') ? $media->title : $this->getMediaTitle($media->file);
                $data_obj->not_found = (int)$not_found;
                $data_obj->is_analyzed = 1;
                $data_obj->extension = $mediamanager->getFileExtension($media->file);
                $data_obj->type = $mediamanager->getFileType($data_obj->extension);

                $factory->saveRecord('#__media_media', $data_obj);
            }
        } else {
            $query = new Query();
            $query->update('#__media_media');
            $query->set('is_analyzed', '0');
            $query->execute();
        }
    }

    function getMediaTitle($media_file) {

        $media_file_arr = array_reverse(explode('/', $media_file));

        $media_title = $media_file_arr[0];

        return $media_title;
    }

    function checkFileExist($media) {

        if (!file_exists(JPATH_ROOT . '/' . $media->file)) {
            return true;
        } else {
            return false;
        }
    }

    function getMedias() {

        $factory = new KazistFactory;
        $db = $factory->getDatabase();

        $query = new Query();
        $query->select('mm.*');
        $query->from('#__media_media', 'mm');
        $query->where('mm.is_analyzed=0 OR mm.is_analyzed IS NULL');

        $query->setFirstResult(0);
        $query->setMaxResults(100);

        $records = $query->loadObjectList();

        return $records;
    }

}
