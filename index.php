<?php

//for debug purposes
ini_set("display_errors",1);
error_reporting(E_ALL);
include_once "autoloader.php";

$collector = new Collector();
//you can define routes, filters and restrictions to Autorouting here
$collector->filter("auth", function(){
    if(session_status() === PHP_SESSION_NONE)
        return false;
});

$defaultPage = "/Home/index";
$query = (isset($_GET["q"])) ? filter_input(INPUT_GET,"q",FILTER_SANITIZE_STRING) : $defaultPage;

$controller = new Controller($collector);
$controller->dispatch($query);