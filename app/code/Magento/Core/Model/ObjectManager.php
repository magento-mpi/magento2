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
class Magento_Core_Model_ObjectManager extends \Magento\ObjectManager\ObjectManager
{
    /**
     * @var \Magento\ObjectManager\Relations
     */
    protected $_compiledRelations;

    /**
     * @param Magento_Core_Model_Config_Primary $primaryConfig
     * @param \Magento\ObjectManager\Config $config
     * @param array $sharedInstances
     * @param Magento_Core_Model_ObjectManager_ConfigLoader_Primary $primaryLoader
     * @throws \Magento\BootstrapException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function __construct(
        Magento_Core_Model_Config_Primary $primaryConfig,
        \Magento\ObjectManager\Config $config = null,
        $sharedInstances = array(),
        Magento_Core_Model_ObjectManager_ConfigLoader_Primary $primaryLoader = null
    ) {
        $definitionFactory = new Magento_Core_Model_ObjectManager_DefinitionFactory($primaryConfig);
        $definitions = $definitionFactory->createClassDefinition($primaryConfig);
        $relations = $definitionFactory->createRelations();
        $config = $config ?: new \Magento\ObjectManager\Config\Config(
            $relations,
            $definitions
        );

        $appMode = $primaryConfig->getParam(Mage::PARAM_MODE, Magento_Core_Model_App_State::MODE_DEFAULT);
        $factory = new \Magento\ObjectManager\Factory\Factory($config, $this, $definitions, $primaryConfig->getParams());

        $sharedInstances['Magento_Core_Model_Config_Primary'] = $primaryConfig;
        $sharedInstances['Magento_Core_Model_Dir'] = $primaryConfig->getDirectories();
        $sharedInstances['Magento_Core_Model_ObjectManager'] = $this;

        parent::__construct($factory, $config, $sharedInstances);
        $primaryConfig->configure($this);

        Mage::setObjectManager($this);

        \Magento\Profiler::start('global_primary');
        $primaryLoader = $primaryLoader ?: new Magento_Core_Model_ObjectManager_ConfigLoader_Primary(
            $primaryConfig->getDirectories(),
            $appMode
        );
        try {
            $configData = $primaryLoader->load();
        } catch (Exception $e) {
            throw new \Magento\BootstrapException($e->getMessage());
        }

        if ($configData) {
            $this->configure($configData);
        }

        $interceptorGenerator = ($definitions instanceof \Magento\ObjectManager\Definition\Compiled)
            ? null
            : new \Magento\Interception\CodeGenerator\CodeGenerator();

        \Magento\Profiler::stop('global_primary');
        $verification = $this->get('Magento_Core_Model_Dir_Verification');
        $verification->createAndVerifyDirectories();

        $interceptionConfig = $this->create('Magento\Interception\Config\Config', array(
            'relations' => $definitionFactory->createRelations(),
            'omConfig' => $this->_config,
            'codeGenerator' => $interceptorGenerator,
            'classDefinitions' => $definitions instanceof \Magento\ObjectManager\Definition\Compiled
                ? $definitions
                : null,
            'cacheId' => 'interception',
        ));

        $pluginList = $this->create('Magento\Interception\PluginList\PluginList', array(
            'relations' => $definitionFactory->createRelations(),
            'definitions' => $definitionFactory->createPluginDefinition(),
            'omConfig' => $this->_config,
            'classDefinitions' => $definitions instanceof \Magento\ObjectManager\Definition\Compiled
                ? $definitions
                : null,
            'scopePriorityScheme' => array('global'),
            'cacheId' => 'pluginlist',
        ));
        $this->_sharedInstances['Magento\Interception\PluginList\PluginList'] = $pluginList;
        $this->_factory = $this->create('Magento\Interception\FactoryDecorator', array(
            'factory' => $this->_factory,
            'config' => $interceptionConfig,
            'pluginList' => $pluginList
        ));
        $this->_config->setCache($this->get('Magento_Core_Model_ObjectManager_ConfigCache'));
        $this->configure($this->get('Magento_Core_Model_ObjectManager_ConfigLoader')->load('global'));
    }
}
