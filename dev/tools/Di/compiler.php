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
$rootDir = realpath(__DIR__ . '/../../../');
use Magento\Tools\Di\Compiler\Log\Log,
    Magento\Tools\Di\Compiler\Log\Writer,
    Magento\Tools\Di\Compiler\Directory,
    Magento\Tools\Di\Code\Scanner,
    Magento\Tools\Di\Definition\Compressor,
    Magento\Tools\Di\Definition\Serializer;

$log = new Log(new Writer\Console());

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
        $log->log(Log::GENERATION_SUCCESS, $entityName);
    } else {
        $log->log(Log::GENERATION_ERROR, $entityName);
    }
}

// Code compilation
$paths = array(
    $rootDir . '/app/code',
    $rootDir . '/lib/Magento',
    $rootDir . '/lib/Mage',
    $rootDir . '/lib/Varien',
    $rootDir . '/var/generation'
);

$directoryCompiler = new Directory();
foreach ($paths as $path) {
    $directoryCompiler->compile($path, $log);
}

$serializer = new Serializer\Standard();
$compressor = new Compressor($serializer);
$output = $compressor->compress($directoryCompiler->getResult());

if (!file_exists(BP . '/var/di/')) {
    mkdir(BP . '/var/di', 0777, true);
}

$dirs = $objectManager->get('Mage_Core_Model_Dir');
$fileName = $dirs->getDir(Mage_Core_Model_Dir::DI) . '/definitions.php';
file_put_contents($fileName, $output);
//Reporter
$log->report();
