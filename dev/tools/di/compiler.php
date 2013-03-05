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
require __DIR__ . '/lib/ArrayDefinitionReader.php';
require __DIR__ . '/lib/UniqueList.php';
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

$objectManager = new Mage_Core_Model_ObjectManager(
    new Magento_ObjectManager_Definition_Runtime(),
    new Mage_Core_Model_Config_Primary(BP, array(Mage::PARAM_BAN_CACHE => true))
);
Mage::setObjectManager($objectManager);
$config = $objectManager->get('Mage_Core_Model_Config');

$definitions = array();
$compiler = new ArrayDefinitionReader();
foreach (glob(BP . '/app/code/*') as $codePoolDir) {
    foreach (glob($codePoolDir . '/*') as $vendorDir) {
        foreach (glob($vendorDir . '/*') as $moduleDir) {
            $moduleName = basename($vendorDir) . '_' . basename($moduleDir);
            if (is_dir($moduleDir) && $config->isModuleEnabled($moduleName)) {
                $definitions = array_merge_recursive($definitions, $compiler->compileModule($moduleDir));
            }
        }
    }
}

$definitions = array_merge_recursive($definitions, $compiler->compileModule(BP . '/lib/Varien'));
$definitions = array_merge_recursive($definitions, $compiler->compileModule(BP . '/lib/Magento'));
$definitions = array_merge_recursive($definitions, $compiler->compileModule(BP . '/lib/Mage'));
if (is_readable(BP . '/var/generation')) {
    $definitions = array_merge_recursive($definitions, $compiler->compileModule(BP . '/var/generation'));
}

$signatureList = new UniqueList();
$resultDefinitions = array();
foreach ($definitions as $className => $definition) {
    $resultDefinitions[$className] = null;
    if (isset($definition['parameters']['__construct']) && count($definition['parameters']['__construct'])) {
        $resultDefinitions[$className] = $signatureList->getNumber(
            array_values($definition['parameters']['__construct'])
        );
    }
}

if (!file_exists(BP . '/var/di/')) {
    mkdir(BP . '/var/di', 0777, true);
}

$output = '';
switch ($opt->getOption('format')) {
    case 'igbinary':
        if (!function_exists('igbinary_serialize')) {
            die('Error: Igbinary extension is not installed');
        }
        $output = igbinary_serialize(array($signatureList->asArray('igbinary_serialize'), $resultDefinitions));
        break;

    case 'serialize':
    default:
        $output = serialize(array($signatureList->asArray('serialize'), $resultDefinitions));
        break;
}

if ($opt->getOption('file')) {
    $dirs = $objectManager->get('Mage_Core_Model_Dir');
    $fileName = strlen($opt->getOption('file')) > 1 ?
        $opt->getOption('file') :
        $dirs->getDir(Mage_Core_Model_Dir::DI) . '/definitions.php';
    file_put_contents($fileName, $output);
} else {
    echo $output;
}
