<?php

class Mage_Core_Model_ObjectManager_DefinitionFactory
{
    /**
     * Predefined definitions
     *
     * @var mixed
     */
    protected $_inputDefinitions;

    /**
     * @param mixed $definitions
     */
    public function __construct($definitions = null)
    {
        $this->_inputDefinitions = $definitions;
    }

    /**
     * @param Mage_Core_Model_Config_Primary $config
     * @return Magento_ObjectManager_Definition_Compiled|Magento_ObjectManager_Definition_Runtime
     */
    public function create(Mage_Core_Model_Config_Primary $config)
    {
        $definitionConfig = $config->getNode('global/di/definitions');
        if (empty($definitionConfig) || !isset($definitionConfig['storage'])) {
            return new Magento_ObjectManager_Definition_Runtime();
        }
        $definitions = null;
        $storageType = isset($definitionConfig['type']) ? $definitionConfig['type'] : null;
        switch ($storageType) {
            case 'input':
                $definitions = $this->_inputDefinitions;
                break;

            case 'file':
            default:
                $definitionsFile = isset($definitionConfig['path']) ?
                    $definitionConfig['path'] :
                    $config->getDirectories()->getDir(Mage_Core_Model_Dir::DI) . '/definitions.php';
                $definitions = file_get_contents($definitionsFile);
        };
        $format = isset($definitionConfig['format']) ? $definitionConfig['format'] : 'serialized';
        switch ($format) {
            case 'igbinary':
                $definitions = igbinary_unserialize($definitions);
                break;

            case 'serialize':
            default:
                $definitions = unserialize($definitions);
                break;
        }
        return new Magento_ObjectManager_Definition_Compiled($definitions);
    }
}
