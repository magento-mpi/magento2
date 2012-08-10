<?php

/**
 * Require necessary files
 */
/**
 * Constants definition
 */
define('DS', DIRECTORY_SEPARATOR);
define('BP', realpath(__DIR__ . '/../../..'));

require_once BP . '/lib/Magento/Autoload.php';
require_once BP . '/app/code/core/Mage/Core/functions.php';
require_once BP . '/app/Mage.php';

$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'local';
$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'community';
$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'core';
$paths[] = BP . DS . 'lib';
Magento_Autoload::getInstance()->addIncludePath($paths);

use \Zend\Di\Di,
\Zend\Di\Definition\CompilerDefinition;
Mage::setRoot();
$config = new Mage_Core_Model_Config();
$config->loadBase();
$config->loadModules();

$definitions = array();
foreach(glob(BP . '/app/code/*') as $codePoolDir) {
    foreach (glob($codePoolDir . '/*') as $vendorDir) {
        foreach (glob($vendorDir . '/*') as $moduleDir) {
            $moduleName = basename($vendorDir) . '_' . basename($moduleDir);
            if (is_dir($moduleDir) && $config->isModuleEnabled($moduleName)) {
                echo "Adding module " . $moduleName . "\n";

                $strategy = new \Zend\Di\Definition\IntrospectionStrategy(new \Zend\Code\Annotation\AnnotationManager());
                $strategy->setMethodNameInclusionPatterns(array());
                $strategy->setInterfaceInjectionInclusionPatterns(array());

                $compiler = new CompilerDefinition($strategy);
                $compiler->addDirectory($moduleDir);

                $controllerPath = $moduleDir . '/controllers/';
                if (file_exists($controllerPath)) {
                    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($controllerPath)) as $file) {
                        if (! $file->isDir()) {
                            require_once $file->getPathname();
                        }
                    }
                }

                $compiler->compile();
                $moduleDefinitions = $compiler->toArrayDefinition()->toArray();
                array_walk($moduleDefinitions, function (&$item) {
                    unset($item['supertypes']);
                });
                $definitions = array_merge_recursive($definitions, $moduleDefinitions);
            }
        }
    }
}

foreach ($definitions as $key => $definition) {
    $definitions[$key] = json_encode($definition);
}
if (!file_exists(BP . '/var/di/')) {
    mkdir(BP . '/var/di', 0777, true);
}

file_put_contents(BP . '/var/di/definitions.php', serialize($definitions));
//file_put_contents(BP . '/var/di/definitions.php', '<?php return ' . var_export($definitions, true) . ';');
