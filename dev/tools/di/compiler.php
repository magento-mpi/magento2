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

$objectManager = new Mage_Core_Model_ObjectManager(new Mage_Core_Model_ObjectManager_Config(array(
    Mage::PARAM_BASEDIR => BP,
    Mage::PARAM_BAN_CACHE => true
)), null);
$config = $objectManager->get('Mage_Core_Model_Config');

$definitions = array();
$compiler = new ArrayDefinitionReader();
foreach (glob(BP . '/app/code/*') as $vendorDir) {
    foreach (glob($vendorDir . '/*') as $moduleDir) {
        $moduleName = basename($vendorDir) . '_' . basename($moduleDir);
        if (is_dir($moduleDir) && $config->isModuleEnabled($moduleName)) {
            echo "Compiling module " . $moduleName . "\n";
            $definitions = array_merge_recursive($definitions, $compiler->compileModule($moduleDir));
        }
    }    
}

echo "Compiling Varien\n";
$definitions = array_merge_recursive($definitions, $compiler->compileModule(BP . '/lib/Varien'));
echo "Compiling Magento\n";
$definitions = array_merge_recursive($definitions, $compiler->compileModule(BP . '/lib/Magento'));
echo "Compiling Mage\n";
$definitions = array_merge_recursive($definitions, $compiler->compileModule(BP . '/lib/Mage'));
if (is_readable(BP . '/var/generation')) {
    echo "Compiling generated entities\n";
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

file_put_contents(
    BP . '/var/di/definitions.php', serialize(array($signatureList->asArray(), $resultDefinitions))
);
