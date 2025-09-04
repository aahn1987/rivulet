<?php

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$kernel   = new Rivulet\Http\Kernel();
$request  = Rivulet\Http\Request::capture();
$response = $kernel->handle($request);

$response->send();
