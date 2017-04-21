<?php

/**
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
use Symfony\Component\HttpFoundation\Request;

class Setup {

    public $container = '';

    public function __construct($sc) {

        $this->container = $sc;

        $request = Request::createFromGlobals();
        $sc->register('request', $request);

        $document = new \stdClass();
        $document->class = 'Install';
        $sc->register('document', $document);
        
        $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        
        $actual_link_arr = explode('install', $actual_link);
        
        define('WEB_ROOT',$actual_link_arr[0]);
        
    }

    function getMessage() {

        $session = $this->container->get('session');

        $message = $session->get('message');

        return $message;
    }

    function clearMessage() {

        $session = $this->container->get('session');

        $session->remove('message');
    }

    function getStep() {

        $request = $this->container->get('request');

        $get = $_GET;
        $step = $get['step'];

        return $step;
    }

    function getAction() {


        $get = $_GET;
        $action = $get['action'];

        return $action;
    }

    function getSaveToFile() {

        $session = $this->container->get('session');

        $database = $session->get('database');
        $profile = $session->get('profile');
        $system = $session->get('system');

        $config_file = JPATH_ROOT . 'setting/config.php';
        $config_sample_file = JPATH_ROOT . 'setting/config_sample.php';

        $config_content = file_get_contents($config_file);
        $config_sample_content = file_get_contents($config_sample_file);

        $setting_str = $this->getSettingStr();

        $new_setting = str_replace('$SYSTEM_CONFIG;', $setting_str, $config_sample_content);

        if (!file_exists($config_file) || is_writable($config_file)) {

            file_put_contents($config_file, $new_setting);

            header('Location:index.php?step=5&token=' . uniqid());
        } else {
            header('Location:index.php&token=' . uniqid());
        }
    }

    function getSettingStr() {
        $session = $this->container->get('session');

        $database = $session->get('database');
        $profile = $session->get('profile');
        $system = $session->get('system');

        $setting .= '/* -------------------------- Database settings ------------------- */' . "\n";
        $setting .= "\n";
        $setting .= '$sc->setParameter(\'database.driver\', \'pdo_mysql\');' . "\n";
        $setting .= '$sc->setParameter(\'database.host\', \'' . $database['host'] . '\');' . "\n";
        $setting .= '$sc->setParameter(\'database.user\', \'' . $database['username'] . '\');' . "\n";
        $setting .= '$sc->setParameter(\'database.password\', \'' . $database['password'] . '\');' . "\n";
        $setting .= '$sc->setParameter(\'database.name\', \'' . $database['name'] . '\');' . "\n";
        $setting .= '$sc->setParameter(\'database.prefix\', \'' . $database['prefix'] . '\');' . "\n";
        $setting .= "\n";
        $setting .= '/* -------------------------- Seo settings ------------------- */' . "\n";
        $setting .= "\n";
        $setting .= '$sc->setParameter(\'seo.title\', \'' . $system['title'] . '\');' . "\n";
        $setting .= '$sc->setParameter(\'seo.description\', \'' . $system['description'] . '\');';

        return $setting;
    }

    function getSave() {

        $session = $this->container->get('session');

        $post = $_POST;

        if (!empty($post['form'])) {

            $step = ($post['form']['step']) ? $post['form']['step'] + 1 : 1;

            if (!empty($post['form']['database'])) {
                $database = $post['form']['database'];
                $session->set('database', $database);
            }

            if (!empty($post['form']['profile'])) {
                $profile = $post['form']['profile'];
                $session->set('profile', $profile);
            }

            if (!empty($post['form']['system'])) {
                $system = $post['form']['system'];
                $session->set('system', $system);
            }

            header('Location: index.php?step=' . $step . '&token=' . uniqid());
        } else {
            header('Location: index.php&token=' . uniqid());
        }
    }

    function stepOne() {

        $object_arr = array();
        $session = $this->container->get('session');

        $object_arr['step'] = $this->getStep();
        $object_arr['unique'] = uniqid();
        $object_arr['database'] = $session->get('database');

        $twig_html = file_get_contents(JPATH_ROOT . 'install/themes/step1.twig');
        $html = $this->renderString($twig_html, $object_arr);

        return $html;
    }

    function stepOneVerification() {

        $session = $this->container->get('session');

        $failed = false;
        $database = $session->get('database');

        if ($database['name'] == '') {
            $message .= 'Name, ';
            $failed = true;
        }
        if ($database['username'] == '') {
            $message .= 'Username, ';
            $failed = true;
        }
        if ($database['password'] == '') {
            $message .= 'Password, ';
            $failed = true;
        }
        if ($database['host'] == '') {
            $message .= 'Host, ';
            $failed = true;
        }
        if ($database['prefix'] == '') {
            $message .= 'Prefix, ';
            $failed = true;
        }

        if ($failed) {
            $message = 'All Field and Required. The Following Fields are Empty: ' . $message;
            $session->set('message', $message);
            header('Location: index.php?step=1&token=' . uniqid());
        }
    }

    function stepTwo() {

        $object_arr = array();
        $session = $this->container->get('session');

        $object_arr['step'] = $this->getStep();
        $object_arr['unique'] = uniqid();
        $object_arr['profile'] = $session->get('profile');

        $twig_html = file_get_contents(JPATH_ROOT . 'install/themes/step2.twig');
        $html = $this->renderString($twig_html, $object_arr);

        return $html;
    }

    function stepTwoVerification() {

        $session = $this->container->get('session');

        $failed = false;
        $profile = $session->get('profile');

        if ($profile['name'] == '') {
            $message .= 'Name, ';
            $failed = true;
        }
        if ($profile['username'] == '') {
            $message .= 'Username, ';
            $failed = true;
        }
        if ($profile['password'] == '') {
            $message .= 'Password, ';
            $failed = true;
        }
        if ($profile['email'] == '') {
            $message .= 'Email, ';
            $failed = true;
        }

        if ($failed) {
            $message = 'All Field and Required. The Following Fields are Empty: ' . $message;
            $session->set('message', $message);
            header('Location: index.php?step=2&token=' . uniqid());
        }
    }

    function stepThree() {

        $object_arr = array();
        $session = $this->container->get('session');

        $object_arr['step'] = $this->getStep();
        $object_arr['unique'] = uniqid();
        $object_arr['system'] = $session->get('system');

        $twig_html = file_get_contents(JPATH_ROOT . 'install/themes/step3.twig');
        $html = $this->renderString($twig_html, $object_arr);

        return $html;
    }

    function stepThreeVerification() {

        $session = $this->container->get('session');

        $failed = false;
        $system = $session->get('system');

        if ($system['title'] == '') {
            $message .= 'Title, ';
            $failed = true;
        }
        if ($system['description'] == '') {
            $message .= 'Description, ';
            $failed = true;
        }

        if ($failed) {
            $message = 'All Field and Required. The Following Fields are Empty: ' . $message;
            $session->set('message', $message);
            header('Location: index.php?step=3&token=' . uniqid());
        }
    }

    function stepFour() {

        $object_arr = array();
        $session = $this->container->get('session');

        $object_arr['step'] = $this->getStep();
        $object_arr['unique'] = uniqid();
        $object_arr['database'] = $session->get('database');
        $object_arr['profile'] = $session->get('profile');
        $object_arr['system'] = $session->get('system');

        $twig_html = file_get_contents(JPATH_ROOT . 'install/themes/step4.twig');
        $html = $this->renderString($twig_html, $object_arr);

        return $html;
    }

    function stepFive() {

        $object_arr = array();
        $session = $this->container->get('session');

        $object_arr['step'] = $this->getStep();
        $object_arr['unique'] = uniqid();
        $object_arr['profile'] = $session->get('profile');
     
        $twig_html = file_get_contents(JPATH_ROOT . 'install/themes/step5.twig');
        $html = $this->renderString($twig_html, $object_arr);

        return $html;
    }

    function renderString($html, $object_arr) {

        $twig = $this->container->get('twig');

        foreach ($object_arr as $key => $object) {
            $twig->set($key, $object);
        }

        $template = $twig->createTemplate($html);

        return $template->render($object_arr);
    }

    function updateImportantTables() {

        $this->doctrine->entity_path = JPATH_ROOT . 'applications/System/Flexviews/Code/Tables';
        $this->doctrine->getEntityManager();

        $this->doctrine->entity_path = JPATH_ROOT . 'applications/System/Flexviews/Positions/Code/Tables';
        $this->doctrine->getEntityManager();

        $this->doctrine->entity_path = JPATH_ROOT . 'applications/System/Routes/Flexviews/Code/Tables';
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

        $this->doctrine->entity_path = JPATH_ROOT . 'applications/Users/Roles/Code/Tables';
        $this->doctrine->getEntityManager();

        $this->doctrine->entity_path = JPATH_ROOT . 'applications/System/Settings/Code/Tables';
        $this->doctrine->getEntityManager();

        $this->doctrine->entity_path = JPATH_ROOT . 'applications/System/Extensions/Code/Tables';
        $this->doctrine->getEntityManager();
    }

}
