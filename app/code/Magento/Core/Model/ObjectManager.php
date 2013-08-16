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
class Magento_Core_Model_ObjectManager extends Magento_ObjectManager_ObjectManager
{
    /**
     * @var Magento_ObjectManager_Relations
     */
    protected $_compiledRelations;

    /**
     * @param Magento_Core_Model_Config_Primary $primaryConfig
     * @param Magento_ObjectManager_Config $config
     * @param array $sharedInstances
     * @param Magento_Core_Model_ObjectManager_ConfigLoader_Primary $primaryLoader
     * @throws Magento_BootstrapException
     */
    public function __construct(
        Magento_Core_Model_Config_Primary $primaryConfig,
        Magento_ObjectManager_Config $config = null,
        $sharedInstances = array(),
        Magento_Core_Model_ObjectManager_ConfigLoader_Primary $primaryLoader = null
    ) {
        $definitionFactory = new Magento_Core_Model_ObjectManager_DefinitionFactory($primaryConfig);
        $definitions = $definitionFactory->createClassDefinition($primaryConfig);
        $config = $config ?: new Magento_ObjectManager_Config_Config(
            $definitionFactory->createRelations(),
            $definitions
        );

        $appMode = $primaryConfig->getParam(Mage::PARAM_MODE, Magento_Core_Model_App_State::MODE_DEFAULT);
        $classBuilder = ($appMode == Magento_Core_Model_App_State::MODE_DEVELOPER)
            ? new Magento_ObjectManager_Interception_ClassBuilder_Runtime()
            : new Magento_ObjectManager_Interception_ClassBuilder_General();

        $factory = new Magento_ObjectManager_Interception_FactoryDecorator(
            new Magento_ObjectManager_Factory_Factory($config, null, $definitions, $primaryConfig->getParams()),
            $config,
            null,
            $definitionFactory->createPluginDefinition($primaryConfig),
            $classBuilder
        );
        $sharedInstances['Magento_Core_Model_Config_Primary'] = $primaryConfig;
        $sharedInstances['Magento_Core_Model_Dir'] = $primaryConfig->getDirectories();
        $sharedInstances['Magento_Core_Model_ObjectManager'] = $this;

        parent::__construct($factory, $config, $sharedInstances);
        $primaryConfig->configure($this);

        Mage::setObjectManager($this);

        Magento_Profiler::start('global_primary');
        $primaryLoader = $primaryLoader ?: new Magento_Core_Model_ObjectManager_ConfigLoader_Primary(
            $primaryConfig->getDirectories(),
            $appMode
        );
        try {
            $configData = $primaryLoader->load();
        } catch (Exception $e) {
            throw new Magento_BootstrapException($e->getMessage());
        }

        if ($configData) {
            $this->configure($configData);
        }

        Magento_Profiler::stop('global_primary');
        $verification = $this->get('Magento_Core_Model_Dir_Verification');
        $verification->createAndVerifyDirectories();
        $this->_config->setCache($this->get('Magento_Core_Model_ObjectManager_ConfigCache'));
        $this->configure($this->get('Magento_Core_Model_ObjectManager_ConfigLoader')->load('global'));
    }
}
