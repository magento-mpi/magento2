<?php
/**
 * {license_notice}
 *
 * @category   Tools
 * @package    DI
 * @copyright  {copyright}
 * @license    {license_link}
 */

require __DIR__ . '/../bootstrap.php';

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
$directoryScanner = new Scanner\DirectoryScanner();
$files = $directoryScanner->scan($dir, array('xml', 'php'));
$entities = array();

$classNamePattern = '([A-Z]{1}[a-zA-Z0-9]*_[A-Z]{1}[a-zA-Z0-9_]*(Proxy|Factory))';
$configScanner = new Scanner\FileScanner($files['xml'], '/[\n\'"<>]{1}' . $classNamePattern . '[\n\'"<>]{1}/');
$codeScanner = new Scanner\FileScanner($files['php'], '/[ \\b\n\'"\(]{1}' . $classNamePattern . '[ \\b\n\'"]{1}/');
$entities = array_merge($codeScanner->collectEntities(), $configScanner->collectEntities());


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