<?php

/*
 * This file is part of Kazist Framework.
 * (c) Dedan Irungu <irungudedan@gmail.com>
 *  For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 * 
 */

/**
 * Description of MediaController
 *
 * @author sbc
 */

namespace Media\Media\Code\Controllers;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\Controller\BaseController;
use Media\Media\Code\Models\MediaModel;
use Kazist\KazistFactory;
use Kazist\Service\Media\MediaManager;

class MediaController extends BaseController {

    public function editAction() {

        $this->model = new MediaModel();

        $item = $this->model->getRecord();
        $item = $this->model->appendAdditionalDetail($item);

        $data_arr['item'] = $item;
        $data_arr['domain_url'] = DOMAIN_URL;

        $this->html = $this->render('Media:Media:Code:views:edit.index.twig', $data_arr);

        $response = $this->response($this->html);



        return $response;
    }

    public function detailAction() {

        $this->model = new MediaModel();
        //print_r($parameters); exit;
        $item = $this->model->getRecord();
        $item = $this->model->appendAdditionalDetail($item);

        $data_arr['item'] = $item;
        $data_arr['domain_url'] = DOMAIN_URL;

        $this->html = $this->render('Media:Media:Code:views:detail.index.twig', $data_arr);

        $response = $this->response($this->html);



        return $response;
    }

    function uploadAction() {

        $media_ids = array();
        $medias = array();
        $upload_detail = array();

        $factory = new KazistFactory;
        $this->model = new MediaModel();
        $media_manager = new MediaManager();

        $upload_detail['name'] = $this->request->get('name');
        $upload_detail['title'] = $this->request->get('title');
        $upload_detail['description'] = $this->request->get('description');
        $upload_detail['route'] = $this->request->get('root_route');

        $uploaddir = 'uploads/' . str_replace('.', '/', $upload_detail['route']);
        $upload_path = JPATH_ROOT . $uploaddir;

        $factory->makeDir($upload_path);

        foreach ($this->request->files as $name => $uploadedFile) {

            $original_name = preg_replace("/[^A-Za-z0-9.]/", '-', $uploadedFile->getClientOriginalName());
            $web_file = $uploaddir . '/' . $original_name;
            
            try {
                $file = $uploadedFile->move($upload_path, $original_name);
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
            }
            

            $upload_detail['file'] = $web_file;
            $upload_detail['extension'] = $uploadedFile->getClientOriginalExtension();
            $upload_detail['type'] = $media_manager->getFileType($upload_detail['extension']);
            $upload_detail['not_found'] = 0;

            $media_ids[] = $factory->saveRecordByEntity('#__media_media', $upload_detail);
        }

        return $this->redirectToRoute('media.media.listing', array('ids' => $media_ids));
    }

    function listingAction() {

        $medias = array();

        $factory = new KazistFactory;

        $this->model = new MediaModel();

        $ids = $this->request->get('ids');

        $offset = $this->request->get('offset', 0);

        list($records, $offset, $limit, $total) = $this->model->getMediaList($ids, '', $offset);

        $medias['records'] = $records;
        $medias['offset'] = $offset;
        $medias['limit'] = $limit;
        $medias['total'] = $total;

        echo json_encode($medias);

        exit;
    }

    public function cronanalysemediaAction() {
        $this->model = new MediaModel();
        $this->model->analyseMedia();

        return $this->json($data);
    }

}
