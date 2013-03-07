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
require __DIR__ . '/../bootstrap.php';
$rootDir = realpath(__DIR__ . '/../../../');
use Magento\Tools\Di\Compiler\Log\Log,
    Magento\Tools\Di\Compiler\Log\Writer,
    Magento\Tools\Di\Code\Scanner,
    Zend\Code\Scanner\FileScanner;

$log = new Log(new Writer\Console());

// Code compilation
$paths = array(
    $rootDir . '/app/code',
    $rootDir . '/lib/Magento',
    $rootDir . '/lib/Mage',
    $rootDir . '/lib/Varien',
);
$def = array();
$prevDir = null;
$processedClasses = array();
foreach ($paths as $path) {
    $rdi = new RecursiveDirectoryIterator(realpath($path));
    $recursiveIterator = new RecursiveIteratorIterator($rdi,1);
    /** @var $item SplFileInfo */
    foreach ($recursiveIterator as $item) {
        if ($recursiveIterator->getDepth() == 2 && $prevDir != $item->getPath()) {
            echo "                                                                                                                                                \r";
            echo "Compiling directory " . $item->getPath() . "\r";
            $prevDir = $item->getPath();
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
        if ($item->isFile() && pathinfo($item->getRealPath(), PATHINFO_EXTENSION) == 'php') {
            $fileScanner = new FileScanner($item->getRealPath());
            $classNames = $fileScanner->getClassNames();
            if (count($classNames)) {
                require_once $item->getRealPath();
            }
            foreach ($classNames as $className) {
                if (isset($processedClasses[$className])) {
                    continue;
                }
                try {
                    $class = new ReflectionClass($className);
                    $def[$className] = null;
                    $constructor = $class->getConstructor();
                    if ($constructor) {
                        $def[$className] = array();
                        /** @var $parameter ReflectionParameter */
                        foreach ($constructor->getParameters() as $parameter) {
                            $def[$className][] = array(
                                $parameter->getName(),
                                ($parameter->getClass() !== null) ? $parameter->getClass()->getName() : null,
                                !$parameter->isOptional(),
                                $parameter->isOptional() ? $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null : null
                            );
                        }
                    }
                } catch (ReflectionException $e) {
                    $log->log(Log::COMPILATION_ERROR, $className, $e->getMessage());
                }
                $processedClasses[$className] = 1;
            }
        }
    }
}

// Code compilation

//Reporter
$log->report();

