<?php
define('APPLICATION_PATH', realpath(implode(DIRECTORY_SEPARATOR, [__DIR__, "..", "src"])));
define('TESTS_PATH', __DIR__);

date_default_timezone_set('Europe/Madrid');

require_once APPLICATION_PATH . '/../vendor/autoload.php';
