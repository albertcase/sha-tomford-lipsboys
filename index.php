<?php
session_start();
use Core\Kernel;
use Core\Request;

require_once __DIR__ . "/vendor/autoload.php";

$kernel = new Kernel();
$request = new Request();
$response = $kernel->handle($request);
$response->send();