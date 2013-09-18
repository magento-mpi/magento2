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
     * @var Magento_Core_Model_ObjectManager
     */
    protected static $_instance;

    /**
     * @var Magento_ObjectManager_Relations
     */
    protected $_compiledRelations;

    /**
     * Retrieve object manager
     *
     * Temporary solution for removing Mage God Object, removed when Serialization problem has resolved
     *
     * @deprecated
     * @return Magento_ObjectManager
     * @throws RuntimeException
     */
    public static function getInstance()
    {
        if (!self::$_instance instanceof Magento_ObjectManager) {
            throw new RuntimeException('ObjectManager isn\'t initialized');
        }
        return self::$_instance;
    }

    /**
     * Set object manager instance
     *
     * @param Magento_ObjectManager $objectManager
     * @throws LogicException
     */
    public static function setInstance(Magento_ObjectManager $objectManager)
    {
        self::$_instance = $objectManager;
    }

    /**
     * @param Magento_Core_Model_Config_Primary $primaryConfig
     * @param Magento_ObjectManager_Config $config
     * @param array $sharedInstances
     * @param Magento_Core_Model_ObjectManager_ConfigLoader_Primary $primaryLoader
     * @throws Magento_BootstrapException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function __construct(
        Magento_Core_Model_Config_Primary $primaryConfig,
        Magento_ObjectManager_Config $config = null,
        $sharedInstances = array(),
        Magento_Core_Model_ObjectManager_ConfigLoader_Primary $primaryLoader = null
    ) {
        $definitionFactory = new Magento_Core_Model_ObjectManager_DefinitionFactory($primaryConfig);
        $definitions = $definitionFactory->createClassDefinition($primaryConfig);
        $relations = $definitionFactory->createRelations();
        $config = $config ?: new Magento_ObjectManager_Config_Config(
            $relations,
            $definitions
        );

        $appMode = $primaryConfig->getParam(Mage::PARAM_MODE, Magento_Core_Model_App_State::MODE_DEFAULT);
        $factory = new Magento_ObjectManager_Factory_Factory($config, $this, $definitions, $primaryConfig->getParams());

        $sharedInstances['Magento_Core_Model_Config_Primary'] = $primaryConfig;
        $sharedInstances['Magento_Core_Model_Dir'] = $primaryConfig->getDirectories();
        $sharedInstances['Magento_Core_Model_ObjectManager'] = $this;

        parent::__construct($factory, $config, $sharedInstances);
        $primaryConfig->configure($this);

        self::setInstance($this);

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

        if ($definitions instanceof Magento_ObjectManager_Definition_Compiled) {
            $interceptorGenerator = null;
        } else {
            $autoloader = new Magento_Autoload_IncludePath();
            $interceptorGenerator = new Magento_Interception_CodeGenerator_CodeGenerator(new Magento_Code_Generator(
                null,
                $autoloader,
                new Magento_Code_Generator_Io(
                    new Magento_Io_File(),
                    $autoloader,
                    $primaryConfig->getDirectories()->getDir(Magento_Core_Model_Dir::GENERATION)
                )
            ));
        }

        Magento_Profiler::stop('global_primary');
        $verification = $this->get('Magento_Core_Model_Dir_Verification');
        $verification->createAndVerifyDirectories();

        $interceptionConfig = $this->create('Magento_Interception_Config_Config', array(
            'relations' => $definitionFactory->createRelations(),
            'omConfig' => $this->_config,
            'codeGenerator' => $interceptorGenerator,
            'classDefinitions' => $definitions instanceof Magento_ObjectManager_Definition_Compiled
                ? $definitions
                : null,
            'cacheId' => 'interception',
        ));

        $pluginList = $this->create('Magento_Interception_PluginList_PluginList', array(
            'relations' => $definitionFactory->createRelations(),
            'definitions' => $definitionFactory->createPluginDefinition(),
            'omConfig' => $this->_config,
            'classDefinitions' => $definitions instanceof Magento_ObjectManager_Definition_Compiled
                ? $definitions
                : null,
            'scopePriorityScheme' => array('global'),
            'cacheId' => 'pluginlist',
        ));
        $this->_sharedInstances['Magento_Interception_PluginList_PluginList'] = $pluginList;
        $this->_factory = $this->create('Magento_Interception_FactoryDecorator', array(
            'factory' => $this->_factory,
            'config' => $interceptionConfig,
            'pluginList' => $pluginList
        ));
        $this->_config->setCache($this->get('Magento_Core_Model_ObjectManager_ConfigCache'));
        $this->configure($this->get('Magento_Core_Model_ObjectManager_ConfigLoader')->load('global'));

        self::setInstance($this);
    }
}
