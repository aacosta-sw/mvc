<?php

include "autoloader.php";

$class = ($_GET["page"]) ? filter_input(INPUT_GET,"page",FILTER_SANITIZE_STRING) : "Home";

$handler = new $class();

$handler->index();