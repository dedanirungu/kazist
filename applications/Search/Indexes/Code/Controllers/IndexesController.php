<?php

/*
 * This file is part of Kazist Framework.
 * (c) Dedan Irungu <irungudedan@gmail.com>
 *  For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 * 
 */

/**
 * Description of IndexesController
 *
 * @author sbc
 */

namespace Search\Indexes\Code\Controllers;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\Controller\BaseController;
use Search\Indexes\Models\Code\IndexesModel;
use Kazist\Service\System\System;
use Kazist\KazistFactory;

class IndexesController extends BaseController {

    public function indexAction() {

        $session = $this->container->get('session');

        $items = $this->model->getRecords();
        $total = $this->model->getTotal();
        $offset = $this->model->getOffset();
        $limit = $this->model->getLimit();

        $search_phrase = $session->get('search_phrase');
        $search_filter = $session->get('search_filter');

        $data_arr['items'] = $items;
        $data_arr['search_phrase'] = $search_phrase;
        $data_arr['search_filter'] = $search_filter;
        $data_arr['offset'] = $offset;
        $data_arr['limit'] = $limit;
        $data_arr['total'] = $total;

        $this->html = $this->render('Search:Indexes:Code:views:table.index.twig', $data_arr);

        $response = $this->response($this->html);

        return $response;
    }

    public function detailAction($id = '') {

        $system = new System();
        $factory = new KazistFactory();

        $item = $this->model->getRecord($id);
        $subset = $this->model->getSubsetById($item->subset_id);
        $record = $this->model->getRecordById($item->record_id, $subset->table_name);

        $unique_name = str_replace('_', '.', $subset->table_name);

        $path = $factory->generateUrl($unique_name . '.detail', null, 0, $record);

        return $this->redirect($path);
    }

    public function contentindexingAction() {

        $this->model->contentIndexing();
        
        return $this->json($data);
    }

}
