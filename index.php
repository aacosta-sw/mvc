<?php

//for debug purposes
ini_set("display_errors",1);
error_reporting(E_ALL);

include "autoloader.php";

$class = (isset($_GET["page"])) ? filter_input(INPUT_GET,"page",FILTER_SANITIZE_STRING) : "Home";

$handler = new $class();

$handler->index();