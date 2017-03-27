<?php

ini_set('display_errors', 'off');

use Symfony\Component\HttpFoundation\Request;

define('KAZIST', true);

$loader = require_once __DIR__ . '/vendor/autoload.php';
$sc = require_once __DIR__ . '/include/container.php';

$request = Request::createFromGlobals();
$sc->register('request', $request);

$response = $sc->get('framework')->handle($request);

$response->send();
