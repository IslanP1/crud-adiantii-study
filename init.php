<?php
use Dotenv\Dotenv;
use Adianti\Core\AdiantiApplicationConfig;

if (version_compare(PHP_VERSION, '8.2.0') == -1)
{
    die ('The minimum version required for PHP is 8.2.0');
}

// load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// define the autoloader
require_once 'lib/adianti/core/AdiantiCoreLoader.php';
spl_autoload_register(array('Adianti\Core\AdiantiCoreLoader', 'autoload'));
Adianti\Core\AdiantiCoreLoader::loadClassMap();

// vendor autoloader
$loader = require 'vendor/autoload.php';
$loader->register();

// apply app configurations
AdiantiApplicationConfig::start();

// define constants
define('PATH', dirname(__FILE__));

setlocale(LC_ALL, 'C');
