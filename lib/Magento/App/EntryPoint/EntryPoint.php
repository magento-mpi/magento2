<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\EntryPoint;

use Magento\App\Dir,
    Magento\App\Config,
    Magento\App\State,
    Magento\ObjectManager\Config\Config as ObjectManagerConfig,
    Magento\ObjectManager\Factory\Factory,
    Magento\ObjectManager,
    Magento\App\EntryPointInterface,
    Magento\Profiler;


class EntryPoint implements EntryPointInterface
{
    /**
     * Application root directory
     *
     * @var string
     */
    protected $_rootDir;

    /**
     * Application parameters
     *
     * @var array
     */
    protected $_parameters;

    /**
     * Application object manager
     *
     * @var ObjectManager
     */
    protected $_locator;

    /**
     * @param string $rootDir
     * @param array $parameters
     * @param ObjectManager $objectManager
     */
    public function __construct(
        $rootDir,
        $parameters,
        ObjectManager $objectManager = null
    ) {
        $this->_rootDir = $rootDir;
        $this->_parameters = $parameters;
        $this->_locator = $objectManager;
    }

    /**
     * Process exception
     *
     * @param \Exception $exception
     */
    protected function _processException(\Exception $exception)
    {
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
        if ($this->_parameters[State::PARAM_MODE] == State::MODE_DEVELOPER) {
            print '<pre>';
            print $exception->getMessage() . "\n\n";
            print $exception->getTraceAsString();
            print '</pre>';
        } else if ($this->_locator) {
            $reportData = array($exception->getMessage(), $exception->getTraceAsString());

            // retrieve server data
            if (isset($_SERVER)) {
                if (isset($_SERVER['REQUEST_URI'])) {
                    $reportData['url'] = $_SERVER['REQUEST_URI'];
                }
                if (isset($_SERVER['SCRIPT_NAME'])) {
                    $reportData['script_name'] = $_SERVER['SCRIPT_NAME'];
                }
            }

            // attempt to specify store as a skin
            try {
                $storeManager = $this->_locator->get('Magento\Core\Model\StoreManager');
                $reportData['skin'] = $storeManager->getStore()->getCode();
                $modelDir = $this->_locator->get('Magento\App\Dir');
                require_once ($modelDir->getDir(Dir::PUB) . DS . 'errors' . DS . 'report.php');
            } catch (\Exception $exception) {
                echo "Unknown error happened.";
            }
        } else {
            echo "Exception happened during application bootstrap.";
        }
    }

    /**
     * Initialize application
     * 
     * @throws \Magento\BootstrapException
     */
    protected function _initialize()
    {
        $directories = new Dir(
            $this->_rootDir,
            isset($this->_parameters[Dir::PARAM_APP_DIRS]) ? $this->_parameters[Dir::PARAM_APP_DIRS] : array(),
            isset($this->_parameters[Dir::PARAM_APP_URIS]) ? $this->_parameters[Dir::PARAM_APP_URIS] : array()
        );

        \Magento\Autoload\IncludePath::addIncludePath(array($directories->getDir(Dir::GENERATION)));

        $options = new Config(
            $this->_parameters,
            new Config\Loader(
                $directories,
                isset($this->_parameters[Config\Loader::PARAM_CUSTOM_FILE])
                    ? $this->_parameters[Config\Loader::PARAM_CUSTOM_FILE]
                    : null
            )
        );

        $definitionFactory = new ObjectManager\DefinitionFactory(
            $directories->getDir(DIR::DI),
            $directories->getDir(DIR::GENERATION),
            $options->get('definition.format', 'serialized')
        );

        $definitions = $definitionFactory->createClassDefinition($options->get('definitions'));
        $relations = $definitionFactory->createRelations();
        $diConfig = new ObjectManagerConfig($relations, $definitions);
        $appMode = $options->get(State::PARAM_MODE, State::MODE_DEFAULT);
        $primaryLoader = new \Magento\App\ObjectManager\ConfigLoader\Primary($directories, $appMode);
        try {
            $configData = $primaryLoader->load();
        } catch (\Exception $e) {
            throw new \Magento\BootstrapException($e->getMessage());
        }

        if ($configData) {
            $diConfig->extend($configData);
        }

        $factory = new Factory($diConfig, null, $definitions, $options->get());

        $locator = new ObjectManager\ObjectManager($factory, $diConfig, array(
            'Magento\App\Config' => $options,
            'Magento\App\Dir' => $directories
        ));
        \Magento\App\ObjectManager::setInstance($locator);

        $verification = $locator->get('Magento\App\Dir\Verification');
        $verification->createAndVerifyDirectories();

        $diConfig->setCache($locator->get('Magento\App\ObjectManager\ConfigCache'));
        $locator->configure(
            $locator->get('Magento\App\ObjectManager\ConfigLoader')->load('global')
        );

        $relations = $definitionFactory->createRelations();

        $interceptionConfig = $locator->create('Magento\Interception\Config\Config', array(
            'relations' => $relations,
            'omConfig' => $diConfig,
            'classDefinitions' => $definitions instanceof ObjectManager\Definition\Compiled
                    ? $definitions
                    : null,
        ));

        $pluginList = $locator->create('Magento\Interception\PluginList\PluginList', array(
            'relations' => $relations,
            'definitions' => $definitionFactory->createPluginDefinition(),
            'omConfig' => $diConfig,
            'classDefinitions' => $definitions instanceof ObjectManager\Definition\Compiled
                    ? $definitions
                    : null,
        ));
        $factory = $locator->create('Magento\Interception\FactoryDecorator', array(
            'factory' => $factory,
            'config' => $interceptionConfig,
            'pluginList' => $pluginList
        ));
        $locator->setFactory($factory);
        $locator->get('Magento\Core\Model\Resource')
            ->setConfig($locator->get('Magento\Core\Model\Config\Resource'));
        return $locator;
    }

    /**
     * Run application
     *
     * @param string $applicationName
     * @return int
     */
    public function run($applicationName)
    {
        try {
            $this->_locator = $this->_locator ?: $this->_initialize();
            return $this->_locator->get($applicationName)->execute();
        } catch (\Exception $e) {
            $this->_processException($e);
            return -1;
        }
    }
}