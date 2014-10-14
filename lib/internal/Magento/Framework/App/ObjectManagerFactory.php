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
use Magento\Framework\Profiler;
use Magento\Framework\Filesystem\DriverPool;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * Class ObjectManagerFactory
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
    protected $_configClassName = 'Magento\Framework\Interception\ObjectManager\Config';

    /**
     * Filesystem directory list
     *
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * Filesystem driver pool
     *
     * @var DriverPool
     */
    protected $driverPool;

    /**
     * Factory
     *
     * @var \Magento\Framework\ObjectManager\Factory
     */
    protected $factory;

    /**
     * Constructor
     *
     * @param DirectoryList $directoryList
     * @param DriverPool $driverPool
     */
    public function __construct(DirectoryList $directoryList, DriverPool $driverPool)
    {
        $this->directoryList = $directoryList;
        $this->driverPool = $driverPool;
    }

    /**
     * Create ObjectManager
     *
     * @param array $arguments
     * @param bool $useCompiled
     * @return \Magento\Framework\ObjectManager
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function create(array $arguments, $useCompiled = true)
    {
        (new \Magento\Framework\Autoload\IncludePath())->addIncludePath(
            array($this->directoryList->getPath(DirectoryList::GENERATION))
        );

        $appArguments = $this->createAppArguments($this->directoryList, $arguments);

        $definitionFactory = new \Magento\Framework\ObjectManager\DefinitionFactory(
            $this->driverPool->getDriver(DriverPool::FILE),
            $this->directoryList->getPath(DirectoryList::DI),
            $this->directoryList->getPath(DirectoryList::GENERATION),
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
        $configData = $this->_loadPrimaryConfig($this->directoryList, $this->driverPool, $argumentMapper, $appMode);

        if ($configData) {
            $diConfig->extend($configData);
        }

        $factoryClass = $diConfig->getPreference('Magento\Framework\ObjectManager\Factory\Factory');
        $this->factory = new $factoryClass(
            $diConfig,
            null,
            $definitions,
            $appArguments->get()
        );

        if ($appArguments->get('MAGE_PROFILER') == 2) {
            $this->factory = new \Magento\Framework\ObjectManager\Profiler\FactoryDecorator(
                $this->factory,
                \Magento\Framework\ObjectManager\Profiler\Log::getInstance()
            );
        }

        $sharedInstances = [
            'Magento\Framework\App\Arguments' => $appArguments,
            'Magento\Framework\App\Filesystem\DirectoryList' => $this->directoryList,
            'Magento\Framework\Filesystem\DirectoryList' => $this->directoryList,
            'Magento\Framework\Filesystem\DriverPool' => $this->driverPool,
            'Magento\Framework\ObjectManager\Relations' => $relations,
            'Magento\Framework\Interception\Definition' => $definitionFactory->createPluginDefinition(),
            'Magento\Framework\ObjectManager\Config' => $diConfig,
            'Magento\Framework\ObjectManager\Definition' => $definitions,
            'Magento\Framework\Stdlib\BooleanUtils' => $booleanUtils,
            'Magento\Framework\ObjectManager\Config\Mapper\Dom' => $argumentMapper,
            $configClass => $diConfig
        ];

        $className = $this->_locatorClassName;
        /** @var \Magento\Framework\ObjectManager $objectManager */
        $objectManager = new $className($this->factory, $diConfig, $sharedInstances);

        $this->factory->setObjectManager($objectManager);
        ObjectManager::setInstance($objectManager);

        $diConfig->setCache($objectManager->get('Magento\Framework\App\ObjectManager\ConfigCache'));
        $objectManager->configure(
            $objectManager->get('Magento\Framework\App\ObjectManager\ConfigLoader')->load('global')
        );
        $objectManager->get('Magento\Framework\Config\ScopeInterface')->setCurrentScope('global');
        $objectManager->get('Magento\Framework\App\Resource')
            ->setCache($objectManager->get('Magento\Framework\App\CacheInterface'));
        $interceptionConfig = $objectManager->get('Magento\Framework\Interception\Config\Config');
        $diConfig->setInterceptionConfig($interceptionConfig);

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
     * Load primary config
     *
     * @param DirectoryList $directoryList
     * @param DriverPool $driverPool
     * @param mixed $argumentMapper
     * @param string $appMode
     * @return array
     * @throws \Magento\Framework\App\InitException
     */
    protected function _loadPrimaryConfig(DirectoryList $directoryList, $driverPool, $argumentMapper, $appMode)
    {
        $configData = null;
        try {
            $fileResolver = new \Magento\Framework\App\Arguments\FileResolver\Primary(
                new \Magento\Framework\Filesystem(
                    $directoryList,
                    new \Magento\Framework\Filesystem\Directory\ReadFactory($driverPool),
                    new \Magento\Framework\Filesystem\Directory\WriteFactory($driverPool)
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
            throw new \Magento\Framework\App\InitException($e->getMessage(), $e->getCode(), $e);
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
     * @return \Magento\Framework\Interception\PluginList\PluginList
     */
    protected function _createPluginList(
        \Magento\Framework\ObjectManager $objectManager,
        \Magento\Framework\ObjectManager\Relations $relations,
        \Magento\Framework\ObjectManager\DefinitionFactory $definitionFactory,
        \Magento\Framework\ObjectManager\Config\Config $diConfig,
        \Magento\Framework\ObjectManager\Definition $definitions
    ) {
        return $objectManager->create(
            'Magento\Framework\Interception\PluginList\PluginList',
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
