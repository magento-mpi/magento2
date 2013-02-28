<?php

class Mage_Core_Model_ObjectManager_DefinitionFactory
{
    public function create(Mage_Core_Model_Config_Primary $config)
    {
        $definitionConfig = $config->getNode('global/di/definition');
        if (empty($definitionConfig) && !isset($definitionConfig['type'])) {
            return new Magento_ObjectManager_Definition_Runtime();
        }
        $definitions = null;
        switch ($definitionConfig['type']) {
            case 'memcached':
                break;
            case 'file':
            default:
                $definitionsFile = isset($definitionConfig['path']) ?
                    $definitionConfig['path'] :
                    $config->getDirectories()->getDir(Mage_Core_Model_Dir::DI) . '/definitions';
                $definitions = include $definitionsFile;
        };
        $format = isset($definitionConfig['format']) ? $definitionConfig['format'] : 'serialized';
        switch ($format) {
            case 'igbinary':
                $definitions = igbinary_unserialize($definitions);
                break;
            case 'serialize':
            default:
                $definitions = unserialize($definitions);
        }
        return new Magento_ObjectManager_Definition_Compiled($definitions);
    }
}
