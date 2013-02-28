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

$objectManager = new Mage_Core_Model_ObjectManager(
    new Magento_ObjectManager_Definition_Runtime(),
    new Mage_Core_Model_Config_Primary(BP, array(Mage::PARAM_BAN_CACHE => true))
);
$config = $objectManager->get('Mage_Core_Model_Config');

$definitions = array();
$compiler = new ArrayDefinitionReader();
foreach (glob(BP . '/app/code/*') as $codePoolDir) {
    foreach (glob($codePoolDir . '/*') as $vendorDir) {
        foreach (glob($vendorDir . '/*') as $moduleDir) {
            $moduleName = basename($vendorDir) . '_' . basename($moduleDir);
            if (is_dir($moduleDir) && $config->isModuleEnabled($moduleName)) {
                echo "Compiling module " . $moduleName . "\n";
                $definitions = array_merge_recursive($definitions, $compiler->compileModule($moduleDir));
            }
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

$definitionFormat = (string) $config->getNode('global/di/definitions/format');
$content = array($signatureList->asArray(), $resultDefinitions);
$output = '';
switch ($definitionFormat) {
    case 'igbinary':
        $output = igbinary_serialize($content);
        break;

    case 'serialize':
    default:
        $output = serialize($content);
        break;
}

$storageType = (string) $config->getNode('global/di/definitions/storage/type');
$storagePath = (string) $config->getNode('global/di/definitions/storage/path');
switch ($storageType) {
    case 'memcached':
        break;

    case 'file':
    default:
        $dirs = $objectManager->get('Mage_Core_Model_Dir');
        $path = $storagePath ?: $dirs->getDir(Mage_Core_Model_Dir::DI) . '/definitions.php';
        file_put_contents($path, $output);
        break;
}
