<?php
/**
 * Magento application object manager. Configures and application application
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mage_Core_Model_ObjectManager extends Magento_ObjectManager_ObjectManager
{
    /**
     * @var Magento_ObjectManager_Relations
     */
    protected $_compiledRelations;

    /**
     * @param Mage_Core_Model_Config_Primary $primaryConfig
     * @param Magento_ObjectManager_Config $config
     * @param array $sharedInstances
     */
    public function __construct(
        Mage_Core_Model_Config_Primary $primaryConfig,
        Magento_ObjectManager_Config $config = null,
        $sharedInstances = array()
    ) {
        $definitionFactory = new Mage_Core_Model_ObjectManager_DefinitionFactory($primaryConfig);
        $definitions = $definitionFactory->createClassDefinition($primaryConfig);
        $config = $config ?: new Magento_ObjectManager_Config_Config();

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
        $sharedInstances['Mage_Core_Model_Config_Primary'] = $primaryConfig;
        $sharedInstances['Mage_Core_Model_Dir'] = $primaryConfig->getDirectories();
        $sharedInstances['Mage_Core_Model_ObjectManager'] = $this;

        parent::__construct($factory, $config, $sharedInstances);
        $primaryConfig->configure($this);

        Mage::setObjectManager($this);

        Magento_Profiler::start('global_primary');
        $primaryLoader = new Mage_Core_Model_ObjectManager_ConfigLoader_Primary($primaryConfig->getDirectories());
        $configData = $primaryLoader->load();
        if ($configData) {
            $this->configure($configData);
        }

        Magento_Profiler::stop('global_primary');
        $verification = $this->get('Mage_Core_Model_Dir_Verification');
        $verification->createAndVerifyDirectories();
        $this->loadArea('global');
    }

    /**
     * Load di area
     *
     * @param string $areaCode
     */
    public function loadArea($areaCode)
    {
        $key = $areaCode . 'DiConfig';
        /** @var Mage_Core_Model_CacheInterface $cache */
        $cache = $this->get('Mage_Core_Model_Cache_Type_Config');
        $data = $cache->load($key);
        if ($data) {
            $this->_config = unserialize($data);
            $this->_factory->setConfig($this->_config);
        } else {
            $configData = $this->get('Mage_Core_Model_ObjectManager_ConfigLoader')->load($areaCode);
            if (count($configData)) {
                $this->_config->extend($configData);
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
