<?php

error_reporting(E_COMPILE_ERROR|E_RECOVERABLE_ERROR|E_ERROR|E_CORE_ERROR);
ini_set('display_errors', 'on');

use Symfony\Component\HttpFoundation\Request;

define('KAZIST', true);

$loader = require_once __DIR__ . '/vendor/autoload.php';
$sc = require_once __DIR__ . '/include/container.php';

$request = Request::createFromGlobals();
$sc->register('request', $request);

$response = $sc->get('framework')->handle($request);

$response->send();
