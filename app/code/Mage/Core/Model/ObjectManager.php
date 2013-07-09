<?php
/**
 * Magento application object manager. Configures and application application
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_ObjectManager extends Magento_ObjectManager_ObjectManager
{
    /**
     * @var Magento_ObjectManager_Relations
     */
    protected $_compiledRelations;

    /**
     * Crate instance of object manager
     *
     * @param Mage_Core_Model_Config_Primary $primaryConfig
     * @return Mage_Core_Model_ObjectManager
     */
    public static function createInstance(Mage_Core_Model_Config_Primary $primaryConfig)
    {
        $definitionFactory = new Mage_Core_Model_ObjectManager_DefinitionFactory($primaryConfig);
        $definitions =  $definitionFactory->createClassDefinition($primaryConfig);
        $config = new Magento_ObjectManager_Config_Config(
            new Magento_ObjectManager_Relations_Runtime(new Magento_Code_Reader_ClassReader())
        );
        
        $appMode = $primaryConfig->getParam(Mage::PARAM_MODE, Mage_Core_Model_App_State::MODE_DEFAULT);
        $classBuilder = ($appMode == Mage_Core_Model_App_State::MODE_DEVELOPER)
            ? new Magento_ObjectManager_Interception_ClassBuilder_Runtime()
            : new Magento_ObjectManager_Interception_ClassBuilder_General();

        $factory = new Magento_ObjectManager_Interception_FactoryDecorator(
            new Magento_ObjectManager_Factory_Factory($config, null, $definitions, $primaryConfig->getParams()),
            $config,
            null,
            $definitionFactory->createPluginDefinition($primaryConfig),
            $classBuilder
        );
        $objectManager =  new Mage_Core_Model_ObjectManager($factory, $config, array(
            'Mage_Core_Model_Config_Primary' => $primaryConfig,
            'Mage_Core_Model_Dir' => $primaryConfig->getDirectories()
        ));
        $primaryConfig->configure($objectManager);
        return $objectManager;
    }

    /**
     * Load di area
     *
     * @param string $areaCode
     * @param Mage_Core_Model_Config $config
     */
    public function loadArea($areaCode, Mage_Core_Model_Config $config)
    {
        $key = $areaCode . 'DiConfig';
        /** @var Mage_Core_Model_CacheInterface $cache */
        $cache = $this->get('Mage_Core_Model_Cache_Type_Config');
        $data = $cache->load($key);
        if ($data) {
            $this->_config = unserialize($data);
            $this->_factory->setConfig($this->_config);
        } else {
            $diNode = $config->getNode($areaCode . '/di');
            if ($diNode) {
                $this->_config->extend($diNode->asArray());
            }
            if ($this->_factory->getDefinitions() instanceof Magento_ObjectManager_Definition_Compiled) {
                if (!$this->_compiledRelations) {
                    $this->_compiledRelations = new Mage_Core_Model_ObjectManager_Relations(
                        $this->get('Mage_Core_Model_Dir')
                    );
                }
                $this->_config->setRelations($this->_compiledRelations);
                foreach ($this->_factory->getDefinitions()->getClasses() as $type) {
                    $this->_config->hasPlugins($type);
                }
                $cache->save(serialize($this->_config), $key);
            }
        }
    }
}
