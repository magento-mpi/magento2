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
namespace Magento\Core\Model;

class ObjectManager extends \Magento\ObjectManager\ObjectManager
{
    /**
     * @var \Magento\Core\Model\ObjectManager
     */
    protected static $_instance;

    /**
     * @var \Magento\ObjectManager\Relations
     */
    protected $_compiledRelations;

    /**
     * @param \Magento\Core\Model\Config\Primary $primaryConfig
     * @param \Magento\ObjectManager\Config $config
     * @param array $sharedInstances
     * @param \Magento\Core\Model\ObjectManager\ConfigLoader\Primary $primaryLoader
     * @throws \Magento\BootstrapException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function __construct(
        \Magento\Core\Model\Config\Primary $primaryConfig,
        \Magento\ObjectManager\Config $config = null,
        $sharedInstances = array(),
        \Magento\Core\Model\ObjectManager\ConfigLoader\Primary $primaryLoader = null
    ) {
        $definitionFactory = new \Magento\Core\Model\ObjectManager\DefinitionFactory($primaryConfig);
        $definitions = $definitionFactory->createClassDefinition($primaryConfig);
        $relations = $definitionFactory->createRelations();
        $config = $config ?: new \Magento\ObjectManager\Config\Config(
            $relations,
            $definitions
        );

        $appMode = $primaryConfig->getParam(\Mage::PARAM_MODE, \Magento\Core\Model\App\State::MODE_DEFAULT);
        $factory = new \Magento\ObjectManager\Factory\Factory($config, $this, $definitions, $primaryConfig->getParams());

        $sharedInstances['Magento\Core\Model\Config\Primary'] = $primaryConfig;
        $sharedInstances['Magento\Core\Model\Dir'] = $primaryConfig->getDirectories();
        $sharedInstances['Magento\Core\Model\ObjectManager'] = $this;

        parent::__construct($factory, $config, $sharedInstances);
        $primaryConfig->configure($this);

        \Mage::setObjectManager($this);

        \Magento\Profiler::start('global_primary');
        $primaryLoader = $primaryLoader ?: new \Magento\Core\Model\ObjectManager\ConfigLoader\Primary(
            $primaryConfig->getDirectories(),
            $appMode
        );
        try {
            $configData = $primaryLoader->load();
        } catch (\Exception $e) {
            throw new \Magento\BootstrapException($e->getMessage());
        }

        if ($configData) {
            $this->configure($configData);
        }

        if ($definitions instanceof \Magento\ObjectManager\Definition\Compiled) {
            $interceptorGenerator = null;
        } else {
            $autoloader = new \Magento\Autoload\IncludePath();
            $interceptorGenerator = new \Magento\Interception\CodeGenerator\CodeGenerator(new \Magento\Code\Generator(
                null,
                $autoloader,
                new \Magento\Code\Generator\Io(
                    new \Magento\Io\File(),
                    $autoloader,
                    $primaryConfig->getDirectories()->getDir(\Magento\Core\Model\Dir::GENERATION)
                )
            ));
        }

        \Magento\Profiler::stop('global_primary');
        $verification = $this->get('Magento\Core\Model\Dir\Verification');
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
        $this->_config->setCache($this->get('Magento\Core\Model\ObjectManager\ConfigCache'));
        $this->configure($this->get('Magento\Core\Model\ObjectManager\ConfigLoader')->load('global'));

        self::$_instance = $this;
    }

    /**
     * Return global instance
     *
     * Temporary solution for removing Mage God Object, removed when Serialization problem has resolved
     *
     * @deprecated
     * @return \Magento\ObjectManager
     */
    public static function getInstance()
    {
        return self::$_instance;
    }
}
