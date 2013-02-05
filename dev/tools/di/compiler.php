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

class ArrayDefinitionCompiler
{
    /**
     * Main config
     *
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $objectManager = new Mage_Core_Model_ObjectManager(new Mage_Core_Model_ObjectManager_Config(array(
            Mage::PARAM_BASEDIR => BP,
            Mage::PARAM_BAN_CACHE => true
        )), BP);
        $this->_config = $objectManager->get('Mage_Core_Model_Config');
    }

    /**
     * Compile module definitions
     *
     * @param string $moduleDir
     * @return array
     */
    public function compileModule($moduleDir)
    {
        return $this->_compileModuleDefinitions($moduleDir);
    }

    /**
     * Check is module enabled
     *
     * @param string $moduleName
     * @return bool
     */
    public function isModuleEnabled($moduleName)
    {
        return $this->_config->isModuleEnabled($moduleName);
    }

    /**
     * Compile definitions using Magento_Di_Definition_CompilerDefinition_Zend
     *
     * @param string $moduleDir
     * @return array
     */
    protected function _compileModuleDefinitions($moduleDir)
    {
        $strategy = new \Zend\Di\Definition\IntrospectionStrategy(new \Zend\Code\Annotation\AnnotationManager());
        $strategy->setMethodNameInclusionPatterns(array());
        $strategy->setInterfaceInjectionInclusionPatterns(array());

        $compiler = new Magento_Di_Definition_CompilerDefinition_Zend($strategy);
        $compiler->addDirectory($moduleDir);

        $controllerPath = $moduleDir . '/controllers/';
        if (file_exists($controllerPath)) {
            /** @var $file DirectoryIterator */
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($controllerPath)) as $file) {
                if (!$file->isDir()) {
                    require_once $file->getPathname();
                }
            }
        }

        $compiler->compile();
        return $compiler->toArray();
    }
}

class UniqueList
{
    protected $_itemsPerNumber = array();

    public function getNumber($item)
    {
        if (in_array($item, $this->_itemsPerNumber)) {
            return array_search($item, $this->_itemsPerNumber);
        } else {
            $this->_itemsPerNumber[] = $item;
            return count($this->_itemsPerNumber)-1;
        }
    }

    public function asArray()
    {
        foreach ($this->_itemsPerNumber as &$item) {
            $item = serialize($item);
        }
        return $this->_itemsPerNumber;
    }
}

$definitions = array();
$compiler = new ArrayDefinitionCompiler();
foreach (glob(BP . '/app/code/*') as $codePoolDir) {
    foreach (glob($codePoolDir . '/*') as $vendorDir) {
        foreach (glob($vendorDir . '/*') as $moduleDir) {
            $moduleName = basename($vendorDir) . '_' . basename($moduleDir);
            if (is_dir($moduleDir) && $compiler->isModuleEnabled($moduleName)) {
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

file_put_contents(
    BP . '/var/di/definitions.php', serialize(array($signatureList->asArray(), $resultDefinitions))
);
