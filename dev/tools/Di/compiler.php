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

$codeScanDir = $rootDir . '/app';
$compilationDirs = array(
    $rootDir . '/app/code',
    $rootDir . '/lib/Magento',
    $rootDir . '/lib/Mage',
    $rootDir . '/lib/Varien',
    $rootDir . '/var/generation'
);
$compiledFile = $rootDir . '/var/di/definitions.php';

try {
    $opt = new Zend_Console_Getopt(array(
        'serializer=w' => 'serializer function that should be used (serialize|binary) default = serialize',
    ));
    $opt->parse();
    $log = new Log(new Writer\Console());
    if ($opt->getOption('serializer') == 'binary') {
        $serializer = new Serializer\Igbinary();
    } else {
        $serializer = new Serializer\Standard();
    }

    // Code generation
    // 1. Code scan
    $directoryScanner = new Scanner\DirectoryScanner();
    $files = $directoryScanner->scan($codeScanDir, array('xml', 'php'));
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

    // 3. Compilation
    $directoryCompiler = new Directory($log);
    foreach ($compilationDirs as $path) {
        if (is_readable($path)) {
            $directoryCompiler->compile($path);
        }
    }

    $compressor = new Compressor($serializer);
    $output = $compressor->compress($directoryCompiler->getResult());
    if (!file_exists(dirname($compiledFile))) {
        mkdir(dirname($compiledFile), 0777, true);
    }

    file_put_contents($compiledFile, $output);
    //Reporter
    $log->report();
} catch (Zend_Console_Getopt_Exception $e) {
    echo $e->getUsageMessage();
    exit(1);
} catch (Exception $e) {
    fwrite(STDERR, "Compiler failed with exception: " . $e->getMessage());
    exit(1);
}
