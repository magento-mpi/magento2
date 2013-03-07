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
require_once __DIR__ . '/ReporterInterface.php';
require_once __DIR__ . '/Reporter/Console.php';
require_once __DIR__ . '/Reporter/Xml.php';

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

/** @var $reporter Tools_Di_ReporterInterface */

switch($opt->getOption('report')) {
    case 'xml':
        $reporter = new Tools_Di_Reporter_Xml();
        break;

    case 'console':
    default:
        $reporter = new Tools_Di_Reporter_Console();
        break;
}

// Code generation
// 1. Code scan
$scanner = new Magento_Code_ScannerComposite();
$scanner->addChild(new Magento_Code_Scanner_Config());
$scanner->addChild(new Magento_Code_Scanner_Php(
    new Zend\Code\Scanner\AggregateDirectoryScanner('C:\wamp\www\m2\app'),
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