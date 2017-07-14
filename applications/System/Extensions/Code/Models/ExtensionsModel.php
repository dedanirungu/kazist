<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LeapYear
 *
 * @author sbc
 */

namespace System\Extensions\Code\Models;

use Kazist\Model\BaseModel;
use Kazist\KazistFactory;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use System\Extensions\Code\Classes\ListExtensions;
use System\Extensions\Code\Classes\AutoDiscover;
use System\Extensions\Code\Classes\FtpNew;
use System\Extensions\Code\Classes\UpdateSystem;

defined('KAZIST') or exit('Not Kazist Framework');

class ExtensionsModel extends BaseModel {

    public $subset_id = '';
    public $extension_id = '';
    public $roles_ids = '';
    public $type = '';
    public $namespace = '';
    public $menu_ids = array();

    public function install($path, $type, $namespace) {

        $this->namespace = $namespace;

        $factory = new KazistFactory();

        $path_arr = explode('.', $path);
        $path_arr = array_map('ucfirst', $path_arr);
        $path_slash = str_replace('.', '/', $path);
        $new_path = JPATH_ROOT . 'applications/' . implode('/', $path_arr);

        $menu_path = $new_path . '/menu.json';
        $manifest_path = $new_path . '/manifest.json';
        $namespace_path = $new_path . '/namespace.json';

        $this->roles_ids['SUPER_ADMIN'] = $this->getRoleId('SUPER_ADMIN');
        $this->roles_ids['ADMIN'] = $this->getRoleId('ADMIN');
        $this->roles_ids['MODERATOR'] = $this->getRoleId('MODERATOR');
        $this->roles_ids['USER'] = $this->getRoleId('USER');

        $extension_data = $factory->getRecord('#__system_extensions', 'se', array('se.path=:path'), array('path' => 'applications/' . trim($path_slash, '/') . '/'));
        $this->extension_id = $extension_data->id;

        switch ($type) {
            case 'prepare':
                $this->prepareTables();
                break;
            case 'menu':
                if (file_exists($menu_path)) {
                    $this->installMenu($new_path, $path, $menu_path);
                }
                break;
            case 'manifest':
                if (file_exists($manifest_path)) {
                    $this->installManifest($new_path, $path, $manifest_path);
                }
                break;
            case 'namespace':
                if (file_exists($namespace_path)) {
                    $this->installNamespace($new_path, $path, $namespace_path);
                }
                break;
            case 'table':
                if (file_exists($namespace_path)) {
                    $this->installTable($namespace_path);
                }
                break;
            case 'addon':
                $this->installAddons($new_path);
                break;
        }
    }

    public function prepareUrl($path = '') {
        $urls = array();

        if ($path == '') {

            $session = $this->container->get('session');
            $application_path = JPATH_ROOT . 'applications/';

            if (is_dir($application_path)) {

                $dir = new \DirectoryIterator($application_path);

                foreach ($dir as $fileinfo) {
                    if ($fileinfo->isDir() && !$fileinfo->isDot()) {
                        $folder = strtolower($fileinfo->getFilename());

                        $urls = array_merge($urls, $this->prepareUrlByPath($folder . '.'));
                    }
                }
            }
        } else {
            $urls = $this->prepareUrlByPath($path);
        }

        return $urls;
    }

    public function prepareUrlByPath($path) {

        $uniqid = uniqid();
        $table_urls = array();
        $namespace_urls = array();

        $path_arr = explode('.', $path);
        $path_arr = array_map('ucfirst', $path_arr);

        $new_path = JPATH_ROOT . 'applications/' . implode('/', $path_arr);
        $addons_path = $new_path . 'Addons/';
        $namespace_path = $new_path . '/namespace.json';

        $urls[] = WEB_BASE . '/admin/system-install/' . $path . '/prepare' . '?' . $uniqid;
        $urls[] = WEB_BASE . '/admin/system-install/' . $path . '/menu' . '?' . $uniqid;
        $urls[] = WEB_BASE . '/admin/system-install/' . $path . '/manifest' . '?' . $uniqid;

        $namespaces = json_decode(file_get_contents($namespace_path));

        foreach ($namespaces as $key => $namespace) {
            $table_urls[] = WEB_BASE . '/admin/system-install/' . $path . '/table/' . $key . '?' . $uniqid;
            $namespace_urls[] = WEB_BASE . '/admin/system-install/' . $path . '/namespace/' . $key . '?' . $uniqid;
        }

        $urls = array_merge($urls, $table_urls, $namespace_urls);

        if (is_dir($addons_path)) {

            $dir = new \DirectoryIterator($addons_path);

            foreach ($dir as $fileinfo) {
                if ($fileinfo->isDir() && !$fileinfo->isDot()) {
                    $folder = ucfirst($fileinfo->getFilename());
                    $urls[] = WEB_BASE . '/admin/system-install/' . $path . '/addon/' . $folder . '?' . $uniqid;
                }
            }
        }

        return $urls;
    }

    public function javascriptRedirector($path, $type, $namespace) {

        $clear_name = str_replace('.', '_', $path);
        $session = $this->container->get('session');
        $urls = $session->get('urls');

        $url = $urls[0];
        unset($urls[0]);
        $urls = array_values($urls);

        $session->set('urls', $urls);

        echo ' <script type="text/javascript">
                   function Redirect() {
                      window.location="' . $url . '";
                   }
                   
                   setTimeout(\'Redirect()\', 1000);
            </script>';

        echo 'Processing <b>' . $path . ':' . $type . ':' . $namespace . '</b> complete.';
        echo '<br>';
        echo '<h4>Redirect Remaining ' . count($urls) . '</h4>';
        echo '<br>';
        echo '<br>';

        echo '<ol>';
        foreach ($urls as $tmp_url) {
            echo '<li>' . $tmp_url . '</ul>';
        }
        echo '</ol>';

        exit;
    }

    public function prepareTables($clear_name = '') {

        $session = $this->container->get('session');
        $table_prepared = $session->get('table_prepared');

        if (!$table_prepared || $clear_name == '') {
            $this->doctrine->entity_path = JPATH_ROOT . 'applications/System/Flexviews/Code/Tables';
            $this->doctrine->getEntityManager();

            $this->doctrine->entity_path = JPATH_ROOT . 'applications/System/Flexviews/Positions/Code/Tables';
            $this->doctrine->getEntityManager();

            $this->doctrine->entity_path = JPATH_ROOT . 'applications/System/Routes/Flexviews/Code/Tables';
            $this->doctrine->getEntityManager();

            $this->doctrine->entity_path = JPATH_ROOT . 'applications/System/Pages/Code/Tables';
            $this->doctrine->getEntityManager();

            $this->doctrine->entity_path = JPATH_ROOT . 'applications/System/Applications/Code/Tables';
            $this->doctrine->getEntityManager();

            $this->doctrine->entity_path = JPATH_ROOT . 'applications/System/Crons/Code/Tables';
            $this->doctrine->getEntityManager();

            $this->doctrine->entity_path = JPATH_ROOT . 'applications/System/Languages/Code/Tables';
            $this->doctrine->getEntityManager();

            $this->doctrine->entity_path = JPATH_ROOT . 'applications/System/Subsets/Code/Tables';
            $this->doctrine->getEntityManager();

            $this->doctrine->entity_path = JPATH_ROOT . 'applications/System/Routes/Code/Tables';
            $this->doctrine->getEntityManager();

            $this->doctrine->entity_path = JPATH_ROOT . 'applications/System/Routes/Permissions/Code/Tables';
            $this->doctrine->getEntityManager();

            $this->doctrine->entity_path = JPATH_ROOT . 'applications/Users/Permission/Code/Tables';
            $this->doctrine->getEntityManager();

            $this->doctrine->entity_path = JPATH_ROOT . 'applications/Users/Roles/Code/Tables';
            $this->doctrine->getEntityManager();

            $this->doctrine->entity_path = JPATH_ROOT . 'applications/System/Settings/Code/Tables';
            $this->doctrine->getEntityManager();

            $this->doctrine->entity_path = JPATH_ROOT . 'applications/System/Extensions/Code/Tables';
            $this->doctrine->getEntityManager();

            $this->doctrine->entity_path = JPATH_ROOT . 'applications/System/Menus/Categories/Code/Tables';
            $this->doctrine->getEntityManager();

            $this->doctrine->entity_path = JPATH_ROOT . 'applications/System/Menus/Code/Tables';
            $this->doctrine->getEntityManager();

            $this->doctrine->entity_path = JPATH_ROOT . 'applications/System/Listeners/Code/Tables';
            $this->doctrine->getEntityManager();

            $this->doctrine->entity_path = JPATH_ROOT . 'applications/Notification/Templates/Layouts/Code/Tables';
            $this->doctrine->getEntityManager();

            $this->doctrine->entity_path = JPATH_ROOT . 'applications/Notification/Subscribers/Harvesters/Code/Tables';
            $this->doctrine->getEntityManager();

            $this->doctrine->entity_path = JPATH_ROOT . 'applications/Notification/Newsletters/Automated/Code/Tables';
            $this->doctrine->getEntityManager();

            $this->doctrine->entity_path = JPATH_ROOT . 'applications/Notification/Newsletters/Scheduled/Code/Tables';
            $this->doctrine->getEntityManager();

            $this->doctrine->entity_path = JPATH_ROOT . 'applications/Notification/Newsletters/Groups/Code/Tables';
            $this->doctrine->getEntityManager();

            $session->set('table_prepared', true);
        }
    }

    public function installMenu($new_path, $path, $menu_path) {

        $factory = new KazistFactory();

        $categories = new \System\Menus\Categories\Code\Tables\Categories();

        $menus_json = json_decode(file_get_contents($menu_path), true);


        $menus_json['grouping'] = ( $menus_json['grouping'] ) ? $menus_json['grouping'] : '';
        $menus_json['published'] = (isset($menus_json['published'])) ? $menus_json['published'] : 1;
        $menus_json['featured'] = 0;
        $menus_json['is_modified'] = 0;

        $menu_category_data = $factory->getRecord('#__system_menus_categories', 'smc', array('smc.alias=:alias'), array('alias' => $menus_json['alias']));

        $factory->bindDataToEntity($categories, $menu_category_data);
        $factory->saveEntity($categories, $menus_json);

        $category_id = $categories->getId();

        $this->processMenu($menus_json['menulist'], $path, $category_id);

        if (!empty($this->menu_ids)) {
            $parameter_arr = array('extension_path' => str_replace('.', '/', $path));
            $where_arr = array('id NOT IN (' . implode(',', $this->menu_ids) . ')', 'extension_path = :extension_path OR extension_path IS NULL', 'is_modified=0');
            $factory->deleteRecords('#__system_menus', $where_arr, $parameter_arr);
        }
    }

    public function processMenu($menus, $path, $category_id = '', $parent_id = 0) {

        $factory = new KazistFactory();

        foreach ($menus as $menu_item) {

            $menus = new \System\Menus\Code\Tables\Menus();

            $menu_item['category_id'] = $category_id;
            $menu_item['parent_id'] = $parent_id;
            $menu_item['status'] = 1;
            $menu_item['extension_path'] = str_replace('.', '/', $path);
            $menu_item['icon'] = '';
            $menu_item['published'] = (isset($menu_item['published'])) ? $menu_item['published'] : 1;
            $menu_item['ordering'] = 0;
            $menu_item['is_modified'] = 0;

            $menu_data = $factory->getRecord('#__system_menus', 'sm', array('sm.alias=:alias', 'sm.is_modified=0'), array('alias' => $menu_item['alias']));
            $factory->bindDataToEntity($menus, $menu_data);
            $factory->saveEntity($menus, $menu_item);

            $this->menu_ids[] = $menus->getId();

            if ($menu_item['children']) {
                $this->processMenu($menu_item['children'], $path, $category_id, $menus->getId());
            }
        }
    }

    public function installTable($namespace_path) {

        $namespaces = json_decode(file_get_contents($namespace_path), true);

        $namespace = $namespaces[$this->namespace];

        $namespace_rewrite = str_replace('\\', '/', $namespace['namespace']);

        $this->doctrine->refresh = true;
        $this->doctrine->entity_path = JPATH_ROOT . 'applications/' . $namespace_rewrite . '/Code/Tables';
        if (is_dir($this->doctrine->entity_path)) {
            $this->doctrine->getEntityManager();
        }
    }

    public function installManifest($new_path, $path, $manifest_path) {

        $factory = new KazistFactory();

        $session = $this->container->get('session');
        $system_route_processed = $session->get('system_route_processed');
        $entityManager = $this->doctrine->getEntityManager();

        $data = json_decode(file_get_contents($manifest_path));
        $path_slash = 'applications/' . str_replace('.', '/', $path);

        $data->description = ($data->title <> '') ? $data->title : $data->description;
        $data->is_core = ($data->is_core <> '') ? $data->is_core : 0;
        $data->is_modified = ($data->is_modified <> '') ? $data->is_modified : 0;
        $extension = new \System\Extensions\Code\Tables\Extensions();
        $extension_data = $factory->getRecord('#__system_extensions', 'se', array('se.path=:path'), array('path' => $path_slash));

        $factory->bindDataToEntity($extension, $extension_data);
        $factory->saveEntity($extension, $data);

        $this->extension_id = $extension->getId();

        if (!$system_route_processed) {
            $this->processSystemRoute();
            $session->set('system_route_processed', true);
        }
    }

    public function processSystemRoute() {
        $system_route_path = JPATH_ROOT . 'include/Kazist/route.json';

        if (file_exists($system_route_path)) {

            $routes = json_decode(file_get_contents($system_route_path), true);

            $this->updateRoute($routes['frontend'], 'frontend', 'MAIN');
            $this->updateRoute($routes['backend'], 'backend', 'MAIN');
        }
    }

    public function installNamespace($new_path, $path, $namespace_path) {
        $factory = new KazistFactory();

        $namespaces = json_decode(file_get_contents($namespace_path));

        foreach ($namespaces as $key => $namespace) {

            if ($this->namespace <> $key) {
                continue;
            }

            $namespace_rewrite = str_replace('\\', '/', $namespace->namespace);
            $manifest_path = JPATH_ROOT . 'applications/' . $namespace_rewrite . '/Code/manifest.json';

            $manifest = json_decode(file_get_contents($manifest_path), true);

            $parameter_arr = array('name' => $manifest['name'], 'extension_id' => $this->extension_id);
            $where_arr = array('ss.name=:name', 'ss.extension_id=:extension_id');
            $subset_data = $factory->getRecord('#__system_subsets', 'ss', $where_arr, $parameter_arr);

            $this->subset_id = (isset($subset_data->id)) ? $subset_data->id : 0;

            if (!isset($subset_data->is_modified) || !$subset_data->is_modified) {

                $manifest['id'] = $subset_data->id;
                $manifest['offset'] = 0;
                $manifest['is_processed'] = 0;
                $manifest['is_modified'] = 0;
                $manifest['path'] = strtolower($namespace_rewrite);
                $manifest['extension_id'] = $this->extension_id;
                $manifest['date_file_modified'] = $manifest['date_file_modified'];

                $this->subset_id = $factory->saveRecord('#__system_subsets', $manifest);
            }

            $this->registerFiles(JPATH_ROOT . 'applications/' . $namespace_rewrite . '/Code/', $namespace_rewrite);
        }
    }

    public function installAddons($new_path) {

        $addons_path = $new_path . 'Addons/';

        $factory = new KazistFactory();

        if (is_dir($addons_path)) {

            $dir = new \DirectoryIterator($addons_path);

            foreach ($dir as $fileinfo) {
                if ($fileinfo->isDir() && !$fileinfo->isDot()) {

                    $folder = ucfirst($fileinfo->getFilename());

                    if ($this->namespace <> $folder) {
                        continue;
                    }

                    $addon_path = $addons_path . $folder . '/';

                    $manifest_path = $addon_path . 'manifest.json';


                    $manifest_data = json_decode(file_get_contents($manifest_path));

                    $namespace_path_arr = explode('/', $manifest_data->path);
                    $namespace_path_arr = array_map('ucfirst', $namespace_path_arr);
                    $namespace_rewrite = implode('\\', $namespace_path_arr);

                    $manifest_data->description = ($manifest_data->title <> '') ? $manifest_data->title : $manifest_data->description;
                    $manifest_data->is_core = ($manifest_data->is_core <> '') ? $manifest_data->is_core : 0;
                    $manifest_data->is_modified = ($manifest_data->is_modified <> '') ? $manifest_data->is_modified : 0;

                    $extension = new \System\Extensions\Code\Tables\Extensions();
                    $extension_data = $factory->getRecord('#__system_extensions', 'se', array('se.path=:path'), array('path' => $manifest_data->path));
                    $factory->bindDataToEntity($extension, $extension_data);
                    $factory->saveEntity($extension, $manifest_data);

                    $this->registerFiles($addon_path, $namespace_rewrite);
                }
            }
        }
    }

    public function registerFiles($file_path, $namespace_rewrite = '') {


        $files['structure'] = $file_path . 'structure.json';
        $files['cron'] = $file_path . 'cron.json';
        $files['data'] = $file_path . 'data.json';
        $files['email'] = $file_path . 'email.json';
        $files['listener'] = $file_path . 'listener.json';
        $files['permission'] = $file_path . 'permission.json';
        $files['language'] = $file_path . 'language.json';
        $files['route'] = $file_path . 'route.json';
        $files['setting'] = $file_path . 'setting.json';
        $files['search'] = $file_path . 'search.json';
        $files['flexview'] = $file_path . 'flexview.json';

        $namespace_path = trim(str_replace('\\', '/', $namespace_rewrite), '/');
        $namespace_path = ($namespace_path != '') ? $namespace_path : 'EMPTY';

        if (file_exists($files['data']) && $namespace_rewrite <> '') {
            $this->updateData($files['data'], $namespace_rewrite);
        }

        if (file_exists($files['cron'])) {
            $this->updateCron($files['cron'], $namespace_path);
        }

        if (file_exists($files['email'])) {
            $this->updateEmail($files['email'], $namespace_path);
        }

        if (file_exists($files['listener'])) {
            $this->updateListener($files['listener'], $namespace_path);
        }

        if (file_exists($files['search'])) {
            $this->updateSearch($files['search'], $namespace_path);
        }


        if (file_exists($files['language'])) {
            $this->updateLanguage($files['language'], $namespace_path);
        }

        if (file_exists($files['setting'])) {
            $this->updateSetting($files['setting'], $namespace_path);
        }

        if (file_exists($files['flexview'])) {
            $this->updateFlexview($files['flexview'], $namespace_path);
        }

        if (file_exists($files['permission'])) {
            $permissions = json_decode(file_get_contents($files['permission']), true);

            if (isset($permissions['USER'])) {
                $this->updatePermission($permissions['USER'], 'USER');
            }
            if (isset($permissions['MODERATOR'])) {
                $this->updatePermission($permissions['MODERATOR'], 'MODERATOR');
            }
            if (isset($permissions['ADMIN'])) {
                $this->updatePermission($permissions['ADMIN'], 'ADMIN');
            }
            if (isset($permissions['SUPER_ADMIN'])) {
                $this->updatePermission($permissions['SUPER_ADMIN'], 'SUPER_ADMIN');
            }
        }

        if (file_exists($files['route'])) {

            $routes = json_decode(file_get_contents($files['route']), true);

            $this->updateRoute($routes['frontend'], 'frontend', $namespace_path);
            $this->updateRoute($routes['backend'], 'backend', $namespace_path);
        }
    }

    public function updateData($data_path, $namespace = '') {

        $factory = new KazistFactory();

        $namespace_tablealias = '';
        $namespace_arr = explode('/', $namespace);
        $namespace_table = str_replace('/', '\\', $namespace) . '\\Code\\Tables\\' . end($namespace_arr);
        $namespace_tablename = strtolower(str_replace('/', '_', $namespace));

        foreach (explode('_', $namespace_tablename) as $v) {
            $namespace_tablealias .= $v[0];
        }

        $datas = json_decode(file_get_contents($data_path), true);


        foreach ($datas as $key => $data) {

            $data['system_tracking_id'] = (isset($data['system_tracking_id']) && $data['system_tracking_id']) ? $data['system_tracking_id'] : $data['tracking_id'];

            if ($key > 500) {
                break;
            }

            $where_arr = array($namespace_tablealias . '.system_tracking_id=:system_tracking_id');
            $parameter_arr = array('system_tracking_id' => $data['system_tracking_id']);

            try {
                $tmp_data = $factory->getRecord('#__' . $namespace_tablename, $namespace_tablealias, $where_arr, $parameter_arr);
            } catch (\Exception $ex) {

                $this->doctrine->entity_path = JPATH_ROOT . 'applications/' . $namespace . '/Code/Tables';
                $this->doctrine->getEntityManager();

                try {
                    $tmp_data = $factory->getRecord('#__' . $namespace_tablename, $namespace_tablealias, $where_arr, $parameter_arr);
                } catch (Exception $ex) {
                    $factory->enqueueMessage('Error Adding data:' . json_encode($data), 'error');
                }
            }

            if (!isset($tmp_data->is_modified) || !$tmp_data->is_modified) {

                $data['id'] = $tmp_data->id;
                $data['is_modified'] = 0;

                try {
                    $factory->saveRecord('#__' . $namespace_tablename, $data);
                } catch (Exception $ex) {
                    $factory->enqueueMessage('Error Adding data:' . json_encode($data), 'error');
                }
            }
        }
    }

    public function updateFlexview($flexview_path, $namespace_rewrite) {

        $factory = new KazistFactory();

        $ids = array();
        $flexview_id = '';
        $flexviews = json_decode(file_get_contents($flexview_path), true);

        foreach ($flexviews as $flexview) {

            $where_arr = array('sf.unique_name=:unique_name');
            $parameter_arr = array('unique_name' => $flexview['unique_name']);
            $flexviews_data_list = $factory->getRecords('#__system_flexviews', 'sf', $where_arr, $parameter_arr);

            if (empty($flexviews_data_list)) {

                $flexview['extension_id'] = $this->extension_id;
                $flexview['render'] = 'flexview';
                $flexview['published'] = ( isset($flexview['published'])) ? $flexview['published'] : 1;
                $flexview['is_modified'] = 0;

                $id = $factory->saveRecord('#__system_flexviews', $flexview);
                $flexviews_data_list = $factory->getRecords('#__system_flexviews', 'sf', array('id=:id'), array('id' => $id));
            }

            foreach ($flexviews_data_list as $flexviews_data) {

                if (!isset($flexviews_data->is_modified) || !$flexviews_data->is_modified) {

                    $flexview['id'] = $flexviews_data->id;
                    $flexview['extension_id'] = $this->extension_id;
                    $flexview['render'] = 'flexview';
                    $flexview['published'] = ( isset($flexview['published'])) ? $flexview['published'] : 1;
                    $flexview['is_modified'] = 0;
                    $flexview['extension_path'] = $namespace_rewrite;
                    $flexview['params'] = json_encode($flexview['params']);
                    $flexview['setting'] = json_encode($flexview['setting']);

                    $ids[] = $flexview_id = $factory->saveRecord('#__system_flexviews', $flexview);

                    if (!empty($flexview['positions'])) {
                        foreach ($flexview['positions'] as $position) {

                            $where_arr = array('sfp.position=:position', 'sfp.flexview_id=:flexview_id');
                            $parameter_arr = array('position' => $position, 'flexview_id' => $flexview_id);
                            $position_data = $factory->getRecord('#__system_flexviews_positions', 'sfp', $where_arr, $parameter_arr);

                            if (!isset($position_data->is_modified) || !$position_data->is_modified) {

                                $tmp_position_data = array();
                                $tmp_position_data['id'] = $position_data->id;
                                $tmp_position_data['position'] = $position;
                                $tmp_position_data['flexview_id'] = $flexview_id;
                                $tmp_position_data['is_modified'] = 0;

                                $factory->saveRecord('#__system_flexviews_positions', $tmp_position_data);
                            }
                        }
                    }

                    if (!empty($flexview['controllers'])) {
                        foreach ($flexview['controllers'] as $controller) {

                            $where_arr = array('srf.route=:route', 'srf.flexview_id=:flexview_id');
                            $parameter_arr = array('route' => $controller['route'], 'flexview_id' => $flexview_id);
                            $controller_data = $factory->getRecord('#__system_routes_flexviews', 'srf', $where_arr, $parameter_arr);

                            if (!isset($controller_data->is_modified) || !$controller_data->is_modified) {

                                $controller['id'] = $controller_data->id;
                                $controller['flexview_id'] = $flexview_id;
                                $controller['is_modified'] = 0;

                                $factory->saveRecord('#__system_routes_flexviews', $controller);
                            }
                        }
                    }
                } elseif (isset($flexviews_data->id) || $flexviews_data->id) {

                    $flexviews_data->setting = json_encode($flexview['setting']);
                    $ids[] = $flexview_id = $factory->saveRecord('#__system_flexviews', $flexviews_data);
                }
            }
        }

        if (!empty($ids)) {
            $parameter_arr = array('extension_path' => $namespace_rewrite); /**/
            $where_arr = array('id NOT IN (' . implode(',', $ids) . ') ', ' extension_path = :extension_path OR extension_path IS NULL', 'is_modified=0');
            $factory->deleteRecords('#__system_flexviews', $where_arr, $parameter_arr);
        }
    }

    public function updateSearch($search_path, $namespace_rewrite) {
        $factory = new KazistFactory();

        $search = json_decode(file_get_contents($search_path), true);

        $where_arr = array('ss.table_name=:table_name', 'ss.alias=:alias');
        $parameter_arr = array('table_name' => $search['table_name'], 'alias' => $search['alias']);
        $search_data = $factory->getRecord('#__search_subsets', 'ss', $where_arr, $parameter_arr);

        $search['id'] = $search_data->id;
        $search['subset_id'] = $this->subset_id;
        $search['is_modified'] = 0;

        $factory->saveRecord('#__search_subsets', $search);
    }

    public function updateCron($cron_path, $namespace_rewrite) {
        $ids = array();
        $factory = new KazistFactory();

        $crons = json_decode(file_get_contents($cron_path), true);

        foreach ($crons as $cron) {


            $where_arr = array('sc.controller=:controller', 'sc.function=:function');
            $parameter_arr = array('controller' => $cron['controller'], 'function' => $cron['function']);
            $cron_data = $factory->getRecord('#__system_crons', 'sc', $where_arr, $parameter_arr);

            if (!isset($cron_data->is_modified) || !$cron_data->is_modified) {
                $cron['extension_path'] = $namespace_rewrite;
                $cron['id'] = $cron_data->id;
                $cron['subset_id'] = $this->subset_id;
                $cron['is_new'] = 0;
                $cron['completed_running'] = 0;
                $cron['locked_key'] = '';
                $cron['is_modified'] = 0;

                $ids[] = $factory->saveRecord('#__system_crons', $cron);
            }
        }
        if (!empty($ids)) {
            $parameter_arr = array('extension_path' => $namespace_rewrite);
            $where_arr = array('id NOT IN (' . implode(',', $ids) . ')', 'extension_path = :extension_path OR extension_path IS NULL', 'is_modified=0');
            $factory->deleteRecords('#__system_crons', $where_arr, $parameter_arr);
        }
    }

    public function updateListener($listener_path, $namespace_rewrite) {

        $ids = array();
        $factory = new KazistFactory();

        $listeners = json_decode(file_get_contents($listener_path), true);

        foreach ($listeners as $key => $listener) {

            $parameter_arr = array('class' => $listener['class']);
            $where_arr = array('sl.class=:class');
            $listener_data = $factory->getRecord('#__system_listeners', 'sl', $where_arr, $parameter_arr);

            if (!isset($listener_data->is_modified) || !$listener_data->is_modified) {

                $listener['extension_path'] = $namespace_rewrite;
                $listener['id'] = $listener_data->id;
                $listener['is_modified'] = 0;
                $listener['published'] = 1;

                $ids[] = $factory->saveRecord('#__system_listeners', $listener);
            }
        }
        if (!empty($ids)) {
            $parameter_arr = array('extension_path' => $namespace_rewrite);
            $where_arr = array('id NOT IN (' . implode(',', $ids) . ')', 'extension_path = :extension_path OR extension_path IS NULL', 'is_modified=0');
            $factory->deleteRecords('#__system_listeners', $where_arr, $parameter_arr);
        }
        return;
    }

    public function updateEmail($email_path, $namespace_rewrite) {

        $emails = json_decode(file_get_contents($email_path), true);

        if (isset($emails['layout'])) {
            $email_layouts = $emails['layout'];
            $this->updateEmailLayout($email_layouts, $namespace_rewrite);
        }

        if (isset($emails['scheduled'])) {
            $email_scheduled = $emails['scheduled'];
            $this->updateEmailScheduled($email_scheduled, $namespace_rewrite);
        }

        if (isset($emails['autonewsletter'])) {
            $email_autonewsletter = $emails['autonewsletter'];
            $this->updateEmailAutonewsletter($email_autonewsletter, $namespace_rewrite);
        }

        if (isset($emails['harvester'])) {
            $email_harvester = $emails['harvester'];
            $this->updateEmailHarvester($email_harvester, $namespace_rewrite);
        }


        return;
    }

    public function updateEmailLayout($emails, $namespace_rewrite) {

        $ids = array();
        $factory = new KazistFactory();

        foreach ($emails as $key => $email) {
            $path = JPATH_ROOT . 'applications/' . $namespace_rewrite . '/Code/emails/' . $email['unique_name'] . '.twig';

            if (file_exists($path)) {
                $email['body'] = file_get_contents($path);
            }

            $parameter_arr = array('unique_name' => $email['unique_name']);
            $where_arr = array('ntl.unique_name=:unique_name');
            $email_data = $factory->getRecord('#__notification_templates_layouts', 'ntl', $where_arr, $parameter_arr);

            if (!isset($email_data->is_modified) || !$email_data->is_modified) {
                $email['subset_id'] = $this->subset_id;
                $email['extension_path'] = $namespace_rewrite;
                $email['id'] = $email_data->id;
                $email['is_modified'] = 0;
                $email['published'] = 1;

                $ids[] = $factory->saveRecord('#__notification_templates_layouts', $email);
            }
        }
        if (!empty($ids)) {
            $parameter_arr = array('extension_path' => $namespace_rewrite);
            $where_arr = array('id NOT IN (' . implode(',', $ids) . ')', 'extension_path = :extension_path OR extension_path IS NULL', 'is_modified=0');
            $factory->deleteRecords('#__notification_templates_layouts', $where_arr, $parameter_arr);
        }
    }

    public function updateEmailScheduled($emails, $namespace_rewrite) {

        $ids = array();
        $factory = new KazistFactory();

        foreach ($emails as $key => $email) {
            $path = JPATH_ROOT . 'applications/' . $namespace_rewrite . '/Code/emails/' . $email['unique_name'] . '.twig';

            if (file_exists($path)) {
                $email['body'] = file_get_contents($path);
            }

            $parameter_arr = array('unique_name' => $email['unique_name']);
            $where_arr = array('nns.unique_name=:unique_name');
            $email_data = $factory->getRecord('#__notification_newsletters_scheduled', 'nns', $where_arr, $parameter_arr);

            if (!isset($email_data->is_modified) || !$email_data->is_modified) {

                $email['subset_id'] = $this->subset_id;
                $email['extension_path'] = strtolower($namespace_rewrite);
                $email['id'] = $email_data->id;
                $email['is_modified'] = 0;
                $email['published'] = 1;
                $email['path'] = strtolower($namespace_rewrite);

                $ids[] = $factory->saveRecord('#__notification_newsletters_scheduled', $email);
            }
        }
        if (!empty($ids)) {
            $parameter_arr = array('extension_path' => $namespace_rewrite);
            $where_arr = array('id NOT IN (' . implode(',', $ids) . ')', 'extension_path = :extension_path OR extension_path IS NULL', 'is_modified=0');
            $factory->deleteRecords('#__notification_newsletters_scheduled', $where_arr, $parameter_arr);
        }
    }

    public function updateEmailAutonewsletter($emails, $namespace_rewrite) {

        $ids = array();
        $factory = new KazistFactory();

        foreach ($emails as $key => $email) {

            $path = JPATH_ROOT . 'applications/' . $namespace_rewrite . '/Code/emails/' . $email['unique_name'] . '.twig';

            if (file_exists($path)) {
                $email['body'] = file_get_contents($path);
            }

            $parameter_arr = array('unique_name' => $email['unique_name']);
            $where_arr = array('nna.unique_name=:unique_name');
            $email_data = $factory->getRecord('#__notification_newsletters_automated', 'nna', $where_arr, $parameter_arr);

            $parameter_arr = array('alias' => $email['frequency']);
            $where_arr = array('nnf.alias=:alias');
            $frequency = $factory->getRecord('#__notification_newsletters_frequencies', 'nnf', $where_arr, $parameter_arr);

            if (!isset($email_data->is_modified) || !$email_data->is_modified) {

                $email['subset_id'] = $this->subset_id;
                $email['extension_path'] = $namespace_rewrite;
                $email['id'] = $email_data->id;
                $email['is_modified'] = 0;
                $email['published'] = 1;
                $email['frequency_id'] = $frequency->id;


                $ids[] = $factory->saveRecord('#__notification_newsletters_automated', $email);
            }
        }
        if (!empty($ids)) {
            $parameter_arr = array('extension_path' => $namespace_rewrite);
            $where_arr = array('id NOT IN (' . implode(',', $ids) . ')', 'extension_path = :extension_path OR extension_path IS NULL', 'is_modified=0');
            $factory->deleteRecords('#__notification_newsletters_automated', $where_arr, $parameter_arr);
        }
    }

    public function updateEmailHarvester($emails, $namespace_rewrite) {

        $ids = array();
        $factory = new KazistFactory();

        foreach ($emails as $key => $email) {

            $parameter_arr = array('subset_id' => $this->subset_id, 'system_tracking_id' => $email->tracking_id);
            $where_arr = array('nsh.subset_id=:subset_id', 'nsh.system_tracking_id=:system_tracking_id');
            $email_data = $factory->getRecord('#__notification_subscribers_harvesters', 'nsh', $where_arr, $parameter_arr);

            if (!isset($email_data->is_modified) || !$email_data->is_modified) {

                $email['system_tracking_id'] = $email->tracking_id;
                $email['subset_id'] = $this->subset_id;
                $email['extension_path'] = $namespace_rewrite;
                $email['id'] = $email_data->id;
                $email['is_modified'] = 0;
                $email['published'] = 1;

                $ids[] = $factory->saveRecord('#__notification_subscribers_harvesters', $email);
            }
        }

        if (!empty($ids)) {
            $parameter_arr = array('extension_path' => $namespace_rewrite);
            $where_arr = array('id NOT IN (' . implode(',', $ids) . ')', 'extension_path = :extension_path OR extension_path IS NULL', 'is_modified=0');
            $factory->deleteRecords('#__notification_subscribers_harvesters', $where_arr, $parameter_arr);
        }
    }

    public function updateLanguage($language_path, $namespace_rewrite) {

        $ids = array();
        $factory = new KazistFactory();

        $tmp_language_path = str_replace(JPATH_ROOT, '', $language_path);

        $where_arr = array('sl.file_path=:file_path');
        $parameter_arr = array('file_path' => $tmp_language_path);
        $language_data = $factory->getRecord('#__system_languages', 'sl', $where_arr, $parameter_arr);

        if (!isset($language_data->is_modified) || !$language_data->is_modified) {

            $language_arr['id'] = $language_data->id;
            $language_arr['file_path'] = $tmp_language_path;
            $language_arr['subset_id'] = $this->subset_id;
            $language_arr['is_modified'] = 0;

            $factory->saveRecord('#__system_languages', $language_arr);
        }
    }

    public function updatePermission($permissions, $role_name) {

        $factory = new KazistFactory();

        $rights = array('can_add', 'can_view', 'can_write', 'can_delete', 'can_viewown', 'can_writeown', 'can_deleteown');
        $role_id = $this->roles_ids[$role_name];

        $parameter_arr = array('role_id' => $role_id, 'subset_id' => $this->subset_id);
        $where_arr = array('up.role_id=:role_id', 'up.subset_id=:subset_id');
        $permission_data = $factory->getRecord('#__users_permission', 'up', $where_arr, $parameter_arr);

        if (!isset($permission_data->is_modified) || !$permission_data->is_modified) {

            $permission_data = array();
            $default_right = ( in_array('all_permission', $permissions)) ? 1 : 0;

            foreach ($rights as $right) {
                $current_right = ( $permissions[$right]) ? $permissions[$right] : $default_right;
                $permission_data[$right] = $current_right;
            }

            $permission_data['id'] = $permission_data->id;
            $permission_data['role_id'] = $role_id;
            $permission_data['subset_id'] = $this->subset_id;
            $permission_data['is_modified'] = 0;

            $factory->saveRecord('#__users_permission', $permission_data);
        }


        return;
    }

    public function updateRoute($routes, $viewside, $namespace_rewrite) {

        $ids = array();
        $factory = new KazistFactory();

        foreach ($routes as $key => $route) {

            $parameter_arr = array('unique_name' => $route['unique_name']);
            $where_arr = array('sr.unique_name=:unique_name');
            $route_data = $factory->getRecord('#__system_routes', 'sr', $where_arr, $parameter_arr);


            if (!isset($route_data->is_modified) || !$route_data->is_modified) {

                $route['arguments'] = json_encode($route['arguments']);
                $route['seo_arguments'] = json_encode($route['seo_arguments']);

                $route['id'] = $route_data->id;
                $route['subset_id'] = $this->subset_id;
                $route['viewside'] = $viewside;
                $route['extension_path'] = $namespace_rewrite;
                $route['published'] = 1;
                $route['is_modified'] = 0;
                $route['is_processed'] = 0;

                $ids[] = $route_id = $factory->saveRecord('#__system_routes', $route);

                if (!empty($route['roles'])) {
                    $this->updateRouteRole($route_id, $route['roles']);
                }
            }
        }

        if (!empty($ids)) {
            $parameter_arr = array("extension_path" => $namespace_rewrite, 'viewside' => $viewside);
            $where_arr = array('id NOT IN (' . implode(',', $ids) . ')', 'viewside = :viewside', "extension_path = :extension_path OR extension_path IS NULL", 'is_modified=0');
            $factory->deleteRecords("#__system_routes", $where_arr, $parameter_arr);
        }
    }

    public function updateRouteRole($route_id, $roles) {

        $factory = new KazistFactory();

        $rights = array('can_add', 'can_view', 'can_write', 'can_delete', 'can_viewown', 'can_writeown', 'can_deleteown');

        foreach ($roles as $role => $permissions) {

            $role_id = $this->roles_ids[$role];


            $parameter_arr = array('role_id' => $role_id, 'route_id' => $route_id);
            $where_arr = array('srp.role_id=:role_id', 'srp.route_id=:route_id');
            $route_role_data = $factory->getRecord('#__system_routes_permissions', 'srp', $where_arr, $parameter_arr);

            if (!isset($route_role_data->is_modified) || !$route_role_data->is_modified) {

                $route_data = array();
                $default_right = ( in_array('all_permission', $permissions)) ? 1 : 0;

                foreach ($rights as $right) {

                    $current_right = ( in_array($right, $permissions)) ? true : $default_right;
                    $route_data[$right] = $current_right;
                }

                $route_data['id'] = $route_role_data->id;
                $route_data['route_id'] = $route_id;
                $route_data['role_id'] = $role_id;

                $factory->saveRecord('#__system_routes_permissions', $route_data);
            }
        }
    }

    public function updateSetting($setting_path, $namespace_rewrite) {

        $ids = array();
        $factory = new KazistFactory();

        $settings = json_decode(file_get_contents($setting_path), true);

        foreach ($settings as $key => $setting) {

            $parameter_arr = array('name' => $key);
            $where_arr = array('ss.name=:name');
            $setting_data = $factory->getRecord('#__system_settings', 'ss', $where_arr, $parameter_arr);

            if (!isset($setting_data->is_modified) || !$setting_data->is_modified) {

                $setting['id'] = $setting_data->id;
                $setting['name'] = $setting['name'];
                $setting['value'] = $setting['default'];
                $setting['extension_path'] = $namespace_rewrite;
                $setting['is_modified'] = 0;
                $setting['subset_id'] = $this->subset_id;

                $ids[] = $factory->saveRecord('#__system_settings', $setting);
            }
        }
        if (!empty($ids)) {
            $parameter_arr = array('extension_path' => $namespace_rewrite);
            $where_arr = array('id NOT IN (' . implode(',', $ids) . ')', 'extension_path = :extension_path OR extension_path IS NULL', 'is_modified=0');
            $factory->deleteRecords('#__system_settings', $where_arr, $parameter_arr);
        }
    }

    public function getRoleId($role) {

        $factory = new KazistFactory();

        $extension_data = $factory->getRecord('#__users_roles', 'ur', array('ur.alias=:alias'), array('alias' => $role));

        return $extension_data->id;
    }

    public function getExtensionList() {

        $factory = new KazistFactory();
        $listextensions = new ListExtensions();

        $extension_data = $factory->getRecords('#__system_extensions', 'se', array('se.extension=:extension'), array('extension' => 'component'), array('se.name'), 0, 100);

        foreach ($extension_data as $key => $extension_item) {
            $extension_id = $extension_item->id;
            $update_data = $factory->getRecord('#__system_extensions_updates', 'seu', array('seu.extension_id=:extension_id'), array('extension_id' => $extension_id), array('seu.id' => 'DESC'));
            $extension_item->installed_version = $update_data->version;
            $extension_data[$extension_item->name] = $extension_item;
            unset($extension_data[$key]);
        }


        $extension_list = $listextensions->getExtensionList();


        foreach ($extension_list['repositories'] as $rep_key => $repository) {
            foreach ($repository->updates as $key => $component) {

                $name = $component['name'];

                $is_installed = (is_object($extension_data[$name])) ? true : false;

                $extension_list['repositories'][$rep_key]->updates[$key]['extension'] = $extension_data[$name]->extension;
                $extension_list['repositories'][$rep_key]->updates[$key]['path'] = $extension_data[$name]->path;
                $extension_list['repositories'][$rep_key]->updates[$key]['icon'] = $extension_data[$name]->icon;
                $extension_list['repositories'][$rep_key]->updates[$key]['installed_id'] = $extension_data[$name]->id;
                $extension_list['repositories'][$rep_key]->updates[$key]['installed_version'] = $extension_data[$name]->installed_version;
                $extension_list['repositories'][$rep_key]->updates[$key]['is_installed'] = $is_installed;
            }
        }

        return json_decode(json_encode($extension_list), true);
    }

    public function updateSystem() {

        $updatesystem = new UpdateSystem();

        return $updatesystem->updateExtensions($this->request);
    }

}
