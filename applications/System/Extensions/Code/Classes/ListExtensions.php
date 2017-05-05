<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace System\Extensions\Code\Classes;

defined('KAZIST') or exit(DIE_MSG);

use Kazist\KazistFactory;
use Kazist\Service\Database\Query;

class ListExtensions {

    public function getExtensionList() {

        $type = 'component';
        $updates = array();

        $repositories = $this->getRepositories();


        foreach ($repositories as $key => $repository) {

            $extensionlist = $this->getOnlineAddonList($repository, $type);
            $repository->updates = $extensionlist;
           
            $updates['repositories'][] = $repository;
        }

        return $updates;
    }

    public function getRepositories() {

        $query = new Query();

        $query->select('*');
        $query->from('#__system_extensions_repositories', 'sar');

        $records = $query->loadObjectList();

        return $records;
    }

    public function getOnlineAddonList($repository, $type) {

        $extensionlist = $this->curlProcessor($repository, $type);

        if ($extensionlist['framework']['name'] <> '') {
            $extensionlist['framework']['installed_version'] = $this->getInstalledVersion($extensionlist['framework']['name'], $extensionlist['framework']['type']);
        }

        foreach ($extensionlist[$type] as $key => $extension) {
            $extensionlist[$type][$key]['installed_version'] = $this->getInstalledVersion($extension['name'], $type);
        }

        return $extensionlist;
    }

    public function getInstalledVersion($name, $type) {

        $query = new Query();

        $query->select('sar.*, sa.name, sa.title, sa.description, sa.is_core, sar.version');
        $query->from('#__system_extensions_updates', 'sar');
        $query->leftJoin('sar', '#__system_extensions', 'sa', 'sa.id=sar.extension_id');
        $query->where('sa.extension = :extension');
        $query->andWhere('sa.name = :name');
        $query->setParameter('extension', $type);
        $query->setParameter('name', $name);
        $query->orderBy('sa.id', 'DESC');

        $record = $query->loadObject();

        if (is_object($record)) {
            return $record->version;
        } else {
            return false;
        }
    }

    public function curlProcessor($repository, $type) {

        $data = array();
        $data['token'] = $repository->token;
        $data['type'] = $type;

        $url = rtrim($repository->url, '/') . '/extension-list';

        $curl = curl_init();
        // Set some options - we are passing in a useragent too here

        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => 'Codular Sample cURL Request',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data
        ));

        // Send the request & save response to $resp
        $resp = curl_exec($curl);

        // Close request to clear up some resources
        curl_close($curl);

        $result = json_decode($resp, true);

        return $result;
    }

}
