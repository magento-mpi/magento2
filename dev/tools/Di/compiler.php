<?php
/**
 * {license_notice}
 *
 * @category   Tools
 * @package    DI
 * @copyright  {copyright}
 * @license    {license_link}
 */

require __DIR__ . '/../../../app/bootstrap.php';
require_once __DIR__  . '/ReporterInterface.php';
require_once __DIR__  . '/Reporter/Console.php';
require_once __DIR__  . '/Reporter/Xml.php';
require_once __DIR__  . '/Code/Scanner/ScannerInterface.php';
require_once __DIR__  . '/Code/Scanner/Composite.php';
require_once __DIR__  . '/Code/Scanner/Config.php';
require_once __DIR__  . '/Code/Scanner/Php.php';

use Magento\Tools\Di;
use Magento\Tools\Di\Code\Scanner;

try {
    $opt = new Zend_Console_Getopt(array(
        'serializer=w' => 'serializer function that should be used (serialize|binary) default = serialize',
        'file|f-s'       => 'write output to file (default = var/di/definitions.php)',
    ));
    $opt->parse();
} catch (Zend_Console_Getopt_Exception $e) {
    echo $e->getUsageMessage();
    exit;
}

/** @var $reporter Magento\Tools\Di\ReporterInterface */

switch($opt->getOption('report')) {
    case 'xml':
        $reporter = new Di\Reporter\Xml();
        break;

    case 'console':
    default:
        $reporter = new Di\Reporter\Console();
        break;
}

// Code generation
// 1. Code scan
$dir = realpath(__DIR__ . '/../../../app');
$scanner = new Scanner\Composite();
$scanner->addChild(new Scanner\Config());
$scanner->addChild(new Scanner\Php(
    new Zend\Code\Scanner\AggregateDirectoryScanner($dir),
    '/(\b|\n|\'|")[A-Z]{1}[a-zA-Z0-9_]*(Proxy|Factory)(\b|\n|\'|")/'
));
$entities = $scanner->collectEntities();

// 2. Generation
$generator = new Magento_Code_Generator();
foreach ($entities as $entityName) {
    if ($generator->generate($entityName)) {
        $reporter->addSuccess($entityName);
    } else {
        $reporter->addError($entityName);
    }
}

// Code compilation

//Reporter
$reporter->report();