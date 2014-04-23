<?php
/**
 * Initialize application object manager.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Profiler;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * Class ObjectManagerFactory
 * @package Magento\Framework\App
 */
class ObjectManagerFactory
{
    /**
     * Locator class name
     *
     * @var string
     */
    protected $_locatorClassName = 'Magento\Framework\ObjectManager\ObjectManager';

    /**
     * Config class name
     *
     * @var string
     */
    protected $_configClassName = 'Magento\Interception\ObjectManager\Config';

    /**
     * Factory
     *
     * @var \Magento\Framework\ObjectManager\Factory
     */
    protected $factory;

    /**
     * Create ObjectManager
     *
     * @param string $rootDir
     * @param array $arguments
     * @param bool $useCompiled
     * @return \Magento\Framework\ObjectManager
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function create($rootDir, array $arguments, $useCompiled = true)
    {
        $directories = isset(
            $arguments[Filesystem::PARAM_APP_DIRS]
        ) ? $arguments[Filesystem::PARAM_APP_DIRS] : array();
        $directoryList = new DirectoryList($rootDir, $directories);

        \Magento\Framework\Autoload\IncludePath::addIncludePath(
            array($directoryList->getDir(Filesystem::GENERATION_DIR))
        );

        $appArguments = $this->createAppArguments($directoryList, $arguments);

        $definitionFactory = new \Magento\Framework\ObjectManager\DefinitionFactory(
            new \Magento\Framework\Filesystem\Driver\File(),
            $directoryList->getDir(Filesystem::DI_DIR),
            $directoryList->getDir(Filesystem::GENERATION_DIR),
            $appArguments->get('definition.format', 'serialized')
        );

        $definitions = $definitionFactory->createClassDefinition($appArguments->get('definitions'), $useCompiled);
        $relations = $definitionFactory->createRelations();
        $configClass = $this->_configClassName;
        /** @var \Magento\Framework\ObjectManager\Config\Config $diConfig */
        $diConfig = new $configClass($relations, $definitions);
        $appMode = $appArguments->get(State::PARAM_MODE, State::MODE_DEFAULT);

        $booleanUtils = new \Magento\Framework\Stdlib\BooleanUtils();
        $argInterpreter = $this->createArgumentInterpreter($booleanUtils);

        $argumentMapper = new \Magento\Framework\ObjectManager\Config\Mapper\Dom($argInterpreter);
        $configData = $this->_loadPrimaryConfig($directoryList, $argumentMapper, $appMode);

        if ($configData) {
            $diConfig->extend($configData);
        }

        $this->factory = new \Magento\Framework\ObjectManager\Factory\Factory(
            $diConfig,
            null,
            $definitions,
            $appArguments->get()
        );

        $className = $this->_locatorClassName;

        $sharedInstances = [
            'Magento\Framework\App\Arguments' => $appArguments,
            'Magento\Framework\App\Filesystem\DirectoryList' => $directoryList,
            'Magento\Framework\Filesystem\DirectoryList' => $directoryList,
            'Magento\Framework\ObjectManager\Relations' => $relations,
            'Magento\Interception\Definition' => $definitionFactory->createPluginDefinition(),
            'Magento\Framework\ObjectManager\Config' => $diConfig,
            'Magento\Framework\ObjectManager\Definition' => $definitions,
            'Magento\Framework\Stdlib\BooleanUtils' => $booleanUtils,
            'Magento\Framework\ObjectManager\Config\Mapper\Dom' => $argumentMapper,
            $configClass => $diConfig
        ];

        /** @var \Magento\Framework\ObjectManager $objectManager */
        $objectManager = new $className($this->factory, $diConfig, $sharedInstances);

        $this->factory->setObjectManager($objectManager);
        ObjectManager::setInstance($objectManager);

        /** @var \Magento\Framework\App\Filesystem\DirectoryList\Verification $verification */
        $verification = $objectManager->get('Magento\Framework\App\Filesystem\DirectoryList\Verification');
        $verification->createAndVerifyDirectories();

        $diConfig->setCache($objectManager->get('Magento\Framework\App\ObjectManager\ConfigCache'));
        $objectManager->configure(
            $objectManager->get('Magento\Framework\App\ObjectManager\ConfigLoader')->load('global')
        );
        $objectManager->get('Magento\Framework\Config\ScopeInterface')->setCurrentScope('global');
        $objectManager->get('Magento\Framework\App\Resource')
            ->setCache($objectManager->get('Magento\Framework\App\CacheInterface'));
        $interceptionConfig = $objectManager->get('Magento\Interception\Config\Config');
        $diConfig->setInterceptionConfig($interceptionConfig);

        $this->configureDirectories($objectManager);

        return $objectManager;
    }

    /**
     * Create instance of application arguments
     *
     * @param DirectoryList $directoryList
     * @param array $arguments
     * @return Arguments
     */
    protected function createAppArguments(DirectoryList $directoryList, array $arguments)
    {
        return new Arguments(
            $arguments,
            new \Magento\Framework\App\Arguments\Loader(
                $directoryList,
                isset(
                    $arguments[\Magento\Framework\App\Arguments\Loader::PARAM_CUSTOM_FILE]
                ) ? $arguments[\Magento\Framework\App\Arguments\Loader::PARAM_CUSTOM_FILE] : null
            )
        );
    }

    /**
     * Return newly created instance on an argument interpreter, suitable for processing DI arguments
     *
     * @param \Magento\Framework\Stdlib\BooleanUtils $booleanUtils
     * @return \Magento\Framework\Data\Argument\InterpreterInterface
     */
    protected function createArgumentInterpreter(
        \Magento\Framework\Stdlib\BooleanUtils $booleanUtils
    ) {
        $constInterpreter = new \Magento\Framework\Data\Argument\Interpreter\Constant();
        $result = new \Magento\Framework\Data\Argument\Interpreter\Composite(
            [
                'boolean' => new \Magento\Framework\Data\Argument\Interpreter\Boolean($booleanUtils),
                'string' => new \Magento\Framework\Data\Argument\Interpreter\String($booleanUtils),
                'number' => new \Magento\Framework\Data\Argument\Interpreter\Number(),
                'null' => new \Magento\Framework\Data\Argument\Interpreter\NullType(),
                'object' => new \Magento\Framework\Data\Argument\Interpreter\Object($booleanUtils),
                'const' => $constInterpreter,
                'init_parameter' => new \Magento\Framework\App\Arguments\ArgumentInterpreter($constInterpreter)
            ],
            \Magento\Framework\ObjectManager\Config\Reader\Dom::TYPE_ATTRIBUTE
        );
        // Add interpreters that reference the composite
        $result->addInterpreter('array', new \Magento\Framework\Data\Argument\Interpreter\ArrayType($result));
        return $result;
    }

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     * @return void
     */
    protected function configureDirectories(\Magento\Framework\ObjectManager $objectManager)
    {
        $directoryList = $objectManager->get('Magento\Framework\App\Filesystem\DirectoryList');
        $directoryListConfig = $objectManager->get('Magento\Framework\App\Filesystem\DirectoryList\Configuration');
        $directoryListConfig->configure($directoryList);
    }

    /**
     * Load primary config
     *
     * @param DirectoryList $directoryList
     * @param mixed $argumentMapper
     * @param string $appMode
     * @return array
     * @throws \Magento\BootstrapException
     */
    protected function _loadPrimaryConfig(DirectoryList $directoryList, $argumentMapper, $appMode)
    {
        $configData = null;
        try {
            $fileResolver = new \Magento\Framework\App\Arguments\FileResolver\Primary(
                new \Magento\Framework\App\Filesystem(
                    $directoryList,
                    new \Magento\Framework\Filesystem\Directory\ReadFactory(),
                    new \Magento\Framework\Filesystem\Directory\WriteFactory()
                ),
                new \Magento\Framework\Config\FileIteratorFactory()
            );
            $schemaLocator = new \Magento\Framework\ObjectManager\Config\SchemaLocator();
            $validationState = new \Magento\Framework\App\Arguments\ValidationState($appMode);

            $reader = new \Magento\Framework\ObjectManager\Config\Reader\Dom(
                $fileResolver,
                $argumentMapper,
                $schemaLocator,
                $validationState
            );
            $configData = $reader->read('primary');
        } catch (\Exception $e) {
            throw new \Magento\BootstrapException($e->getMessage());
        }
        return $configData;
    }

    /**
     * Crete plugin list object
     *
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param \Magento\Framework\ObjectManager\Relations $relations
     * @param \Magento\Framework\ObjectManager\DefinitionFactory $definitionFactory
     * @param \Magento\Framework\ObjectManager\Config\Config $diConfig
     * @param \Magento\Framework\ObjectManager\Definition $definitions
     * @return \Magento\Interception\PluginList\PluginList
     */
    protected function _createPluginList(
        \Magento\Framework\ObjectManager $objectManager,
        \Magento\Framework\ObjectManager\Relations $relations,
        \Magento\Framework\ObjectManager\DefinitionFactory $definitionFactory,
        \Magento\Framework\ObjectManager\Config\Config $diConfig,
        \Magento\Framework\ObjectManager\Definition $definitions
    ) {
        return $objectManager->create(
            'Magento\Interception\PluginList\PluginList',
            [
                'relations' => $relations,
                'definitions' => $definitionFactory->createPluginDefinition(),
                'omConfig' => $diConfig,
                'classDefinitions' => $definitions instanceof
                \Magento\Framework\ObjectManager\Definition\Compiled ? $definitions : null
            ]
        );
    }
}
