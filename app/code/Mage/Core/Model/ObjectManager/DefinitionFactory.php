<?php
/**
 * Object manager definition factory
 *
 * {license_notice}
 *
 * @copyright {@copyright}
 * @license   {@license_link}
 */
class Mage_Core_Model_ObjectManager_DefinitionFactory
{
    /**
     * Get definition model name
     *
     * @param string $format
     * @return string
     */
    protected function _getDefinitionModel($format)
    {
        switch ($format) {
            case 'igbinary':
                return 'Magento_ObjectManager_Definition_Compiled_Binary';

            case 'serialized':
            default:
                return 'Magento_ObjectManager_Definition_Compiled_Serialized';
        }
    }

    /**
     * Get value from array
     *
     * @param array $source
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function _getValue($source, $key, $default = null)
    {
        return array_key_exists($key, $source) ? $source['key'] : $default;
    }
    
    /**
     * @param Mage_Core_Model_Config_Primary $config
     * @return Magento_ObjectManager_Definition
     */
    public function create(Mage_Core_Model_Config_Primary $config)
    {
        Magento_Profiler::start('di_definitions_create');
        $configElement = $config->getNode('global/di/definitions');

        $definitionConfig = $configElement ? $configElement->asArray() : array();
        $format = $this->_getValue($definitionConfig, 'format', 'serialized');
        $definitionModel = $this->_getDefinitionModel($format);
        $definitions = $config->getParam('definitions', false);
        if (false === $definitions) {
            $storageConfig = $this->_getValue($definitionConfig, 'storage', array());
            $storageType = $this->_getValue($storageConfig, 'type', 'file');
            switch ($storageType) {
                case 'file':
                default:
                    $definitionsFile = $this->_getValue($definitionConfig, 'path', false) ?:
                        $config->getDirectories()->getDir(Mage_Core_Model_Dir::DI)
                            . DIRECTORY_SEPARATOR
                            . 'definitions.php';
                    if (is_readable($definitionsFile)) {
                        $definitions = file_get_contents($definitionsFile);
                    } else {
                        $genDir = $config->getDirectories()->getDir(Mage_Core_Model_Dir::VAR_DIR) . '/generation';
                        $autoloader = new Magento_Autoload_IncludePath();
                        $generatorIo = new Magento_Di_Generator_Io(new Varien_Io_File(), $autoloader, $genDir);
                        $generator = new Magento_Di_Generator(null, $autoloader, $generatorIo);
                        return new Magento_ObjectManager_Definition_Runtime(null, $generator);
                    }
                    break;
            };
            switch ($format) {
                case 'igbinary':
                    $definitions = igbinary_unserialize($definitions);
                    break;

                case 'serialized':
                default:
                    $definitions = unserialize($definitions);
                    break;
            }
        }
        $output = new $definitionModel($definitions);
        Magento_Profiler::stop('di_definitions_create');
        return $output;
    }
}
