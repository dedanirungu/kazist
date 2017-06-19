<?php

ini_set('display_errors', 'off');

use Symfony\Component\HttpFoundation\Request;
use Kazist\Service\System\Cron;

$loader = require_once __DIR__ . '/vendor/autoload.php';

$sc = require_once __DIR__ . '/include/container.php';

$request = Request::createFromGlobals();
$sc->register('request', $request);

$sc->get('framework')->handle($request);

$cron = new Cron($sc, $request);
$cron->processCron();

exit;
