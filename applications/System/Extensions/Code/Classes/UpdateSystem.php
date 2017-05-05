<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace System\Extensions\Code\Classes;

defined('KAZIST') or exit(DIE_MSG);

use Kazist\KazistFactory;
use System\Extensions\Code\Classes\AutoDiscover;
use Kazist\Service\Database\Query;
use Composer\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput as Output;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of Update
 *
 * @author sbc
 */
class UpdateSystem {

    public $destination_file = '';

    public function updateExtensions($request) {

        set_time_limit(0);

        $autodiscover = new AutoDiscover();
        $factory = new KazistFactory();

        $extract_path = JPATH_ROOT . '/updater/';
        $factory->makeDir($extract_path, 0777);
       print_r('$extract_path'); exit();

        $id = $request->get('id');
        $installed_id = $request->get('installed_id');
        $repository_id = $request->get('repository_id');

        $repository = $this->getRepository($repository_id);

        $extension = $this->getExtensionCurl($id, $repository);

        $is_deleted = $this->recursiveDelete(realpath($extract_path));

        $factory->makeDir($extract_path, 0777);

        if (!$is_deleted) {
            $factory->enqueueMessage('Addon Not Installed/update. Please clear uploads/updates folder.', 'error');
            return false;
        }

        $composer_content = file_get_contents($extension['composer_path']);

        file_put_contents($extract_path . '/composer.json', $composer_content);

    }

    public function saveAddonToDatabase($result) {

        $factory = new KazistFactory;

        $extension_obj = new \stdClass();
        $update_obj = new \stdClass();

        $extension_id = $this->checkAddonExist($result);

        $extension_obj->id = $extension_id;
        $extension_obj->name = $result['name'];
        $extension_obj->title = $result['title'];
        $extension_obj->description = $result['description'];
        $extension_obj->extension = $result['type'];
        $extension_obj->path = $result['path'];
        $extension_obj->icon = $result['icon'];
        $extension_obj->version = $result['extension_version'];
        $extension_id = $factory->saveRecord('#__system_extensions', $extension_obj);

        $update_obj->extension_id = $extension_id;
        $update_obj->change_log = $result['change_log'];
        $update_obj->version = $result['update_version'];
        $factory->saveRecord('#__system_extensions_updates', $update_obj);

        return$extension_id;
    }

    function getExtensionCurl($id, $repository) {

        $data = array();
        $data['token'] = $repository->token;
        $data['type'] = $type;
        $data['id'] = $id;

        $url = rtrim($repository->url, '/') . '/extension-single';

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

    function getExtension($id) {

        $query = new Query();

        $query->select('*');
        $query->from('#__system_extensions', 'se');
        $query->where('id=:id');
        $query->setParameter('id', $id);

        $record = $query->loadObject();

        return $record;
    }

    function checkAddonExist($result) {
        $query = new Query();

        $query->select('*');
        $query->from('#__system_extensions', 'se');
        $query->where('name=:name');
        $query->andWhere('extension=:extension');
        $query->setParameter('name', $result['name']);
        $query->setParameter('extension', $result['type']);

        $record = $query->loadObject();

        if (is_object($record)) {
            return $record->id;
        } else {
            return false;
        }
    }

    function uploadViaFtp($name, $type) {

        global $sc;

        unlink($this->destination_file);

        $server = $sc->getParameter('ftp.host');
        $ftp_user_name = $sc->getParameter('ftp.username');
        $ftp_user_pass = $sc->getParameter('ftp.password');
        $ftp_directory = $sc->getParameter('ftp.directory');

        $extract_path = JPATH_ROOT . '/uploads/updates/';

        $ftp = new FtpNew($server);

        $ftpSession = $ftp->login($ftp_user_name, $ftp_user_pass);

        if (!$ftpSession) {
            die("Failed to connect.");
        }

        if ($type == 'framework') {

            $ftp->recursiveDelete($ftp_directory . '/applications/Media/');
            $ftp->recursiveDelete($ftp_directory . '/applications/Notification/');
            $ftp->recursiveDelete($ftp_directory . '/applications/Setup/');
            $ftp->recursiveDelete($ftp_directory . '/applications/Search/');
            $ftp->recursiveDelete($ftp_directory . '/applications/Users/');
            $ftp->recursiveDelete($ftp_directory . '/assets/');
            $ftp->recursiveDelete($ftp_directory . '/admin/');
            $ftp->recursiveDelete($ftp_directory . '/include/Addons/');
            $ftp->recursiveDelete($ftp_directory . '/include/Kazist/');
            $ftp->recursiveDelete($ftp_directory . '/include/Setup/');
            $ftp->recursiveDelete($ftp_directory . '/include/Templates/');
            $ftp->recursiveDelete($ftp_directory . '/install/install/');
            $ftp->recursiveDelete($ftp_directory . '/themes/');
            $ftp->recursiveDelete($ftp_directory . '/viewerjs/');
            $ftp->recursiveDelete($ftp_directory . '/uploads/icons/');

            $errorList = $ftp->send_recursive_directory($extract_path, $ftp_directory);
        } elseif ($type == 'vendor') {
            $ftp->recursiveDelete($ftp_directory . '/vendor/');
            $errorList = $ftp->send_recursive_directory($extract_path, $ftp_directory);
        } else {

            $application_file = $ftp_directory . '/applications/' . ucfirst($name);

            $ftp->recursiveDelete($application_file);
            $ftp->make_directory($application_file);

            $errorList = $ftp->send_recursive_directory($extract_path, $application_file);
        }

        $ftp->disconnect();
    }

    function registerSystemSetting($type, $name) {

        $application = new Applications();
        $register = new Register();

        if ($type == 'framework') {
            $register->registerAddons();
        } else {
            $application->register($name, true);
        }
    }

    function recursiveDelete($dir) {

        if (is_dir($dir)) {

            $it = new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);

            foreach ($files as $file) {
                if ($file->isDir()) {
                    rmdir($file->getRealPath());
                } else {
                    if (!unlink($file->getRealPath())) {
                        return false;
                    }
                }
            }

            rmdir($dir);

            return true;
        }
    }

    public function getRepository($repository_id) {

        $query = new Query();

        $query->select('*');
        $query->from('#__system_extensions_repositories', 'sar');
        $query->where('id=:id');
        $query->setParameter('id', $repository_id);

        $record = $query->loadObject();

        return $record;
    }

    public function curlProcessor($repository, $type, $name) {

        $factory = new Kazistfactory();

        $data = array();
        $data['token'] = $repository->token;
        $data['type'] = $type;
        $data['name'] = $name;

        $destination_dir = JPATH_ROOT . 'uploads/updates';

        $factory->makeDir($destination_dir);
        chmod($destination_dir, 0777);

        $this->destination_file = $destination_dir . '/' . $type . '_' . $name . '.zip';
        $url = rtrim($repository->url, '/') . '/extension-download';

        if ($ch === false) {
            $factory->enqueueMessage('Failed to create curl handle', 'error');
        }

        touch($this->destination_file);
        chmod($this->destination_file, 0777);
        $fp = fopen($this->destination_file, 'wb');

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, false);
        curl_setopt($ch, CURLOPT_REFERER, $repository->url);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 50000);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $resp = curl_exec($ch);

        curl_close($ch);
        fclose($fp);

        $result = json_decode($resp, true);

        return $result;
    }

}
