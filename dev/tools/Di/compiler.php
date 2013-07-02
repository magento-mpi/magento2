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

$filePatterns = array(
    'php' => '/.*\.php$/',
    'etc' => '/\/app\/etc\/.*\.xml$/',
    'config' => '/\/etc\/(config([a-z0-9\.]*)?|adminhtml\/system)\.xml$/',
    'view' => '/\/view\/[a-z0-9A-Z\/\.]*\.xml$/',
    'design' => '/\/app\/design\/[a-z0-9A-Z\/\.]*\.xml$/',
);
$codeScanDir = realpath($rootDir . '/app');
$compilationDirs = array(
    $rootDir . '/app/code',
    $rootDir . '/lib/Magento',
    $rootDir . '/lib/Mage',
    $rootDir . '/lib/Varien',
    $rootDir . DS . Magento_Code_Generator_Io::DEFAULT_DIRECTORY // should be added to include path in app bootstrap.php
);
// $compiledFile value should correspond Mage_Core_Model::getDir('di').'definitions.php' of running application
$compiledFile = $rootDir . '/var/di/definitions.php';

try {
    $opt = new Zend_Console_Getopt(array(
        'serializer=w' => 'serializer function that should be used (serialize|binary) default = serialize',
        'verbose|v' => 'output report after tool run',
        'extra-classes-file=s' => 'path to file with extra proxies and factories to generate',
    ));
    $opt->parse();
    $writer = $opt->getOption('v') ? new Writer\Console() : new Writer\Quiet();
    $log = new Log($writer);
    $serializer = ($opt->getOption('serializer') == 'binary') ? new Serializer\Igbinary() : new Serializer\Standard();

    // 1 Code generation
    // 1.1 Code scan
    $directoryScanner = new Scanner\DirectoryScanner();
    $files = $directoryScanner->scan($codeScanDir, $filePatterns);
    $files['additional'] = array($opt->getOption('extra-classes-file'));
    $entities = array();

    $scanner = new Scanner\CompositeScanner();
    $scanner->addChild(new Scanner\PhpScanner(), 'php');
    $scanner->addChild(new Scanner\XmlScanner(), 'etc');
    $scanner->addChild(new Scanner\XmlScanner(), 'config');
    $scanner->addChild(new Scanner\XmlScanner(), 'view');
    $scanner->addChild(new Scanner\XmlScanner(), 'design');
    $scanner->addChild(new Scanner\ArrayScanner(), 'additional');
    $entities = $scanner->collectEntities($files);

    // 1.2 Generation
    $generator = new Magento_Code_Generator();
    foreach ($entities as $entityName) {
        switch ($generator->generateClass($entityName)) {
            case Magento_Code_Generator::GENERATION_SUCCESS:
                $log->add(Log::GENERATION_SUCCESS, $entityName);
                break;

            case Magento_Code_Generator::GENERATION_ERROR:
                $log->add(Log::GENERATION_ERROR, $entityName);
                break;

            case Magento_Code_Generator::GENERATION_SKIP:
            default:
                //no log
                break;
        }
    }

    // 2. Compilation
    // 2.1 Code scan
    $directoryCompiler = new Directory($log);
    foreach ($compilationDirs as $path) {
        if (is_readable($path)) {
            $directoryCompiler->compile($path);
        }
    }

    // 2.2 Compression
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
