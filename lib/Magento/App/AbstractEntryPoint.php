<?php
/**
 * Abstract application entry point
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

use Magento\App\Config as Options;
use Magento\App\Dir;
use Magento\ObjectManager\Config\Config;
use Magento\ObjectManager\Factory\Factory;
use Magento\ObjectManager\ObjectManager;

abstract class AbstractEntryPoint
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
    protected $_objectManager;

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
        $this->_objectManager = $objectManager;
    }

    /**
     * Process request by the application
     */
    public function processRequest()
    {
        $this->_init();
        $this->_processRequest();
    }

    /**
     * Process exception
     *
     * @param \Exception $exception
     */
    public function processException(\Exception $exception)
    {
        $this->_init();
        $appMode = $this->_objectManager->get('Magento\App\State')->getMode();
        if ($appMode == \Magento\App\State::MODE_DEVELOPER) {
            print '<pre>';
            print $exception->getMessage() . "\n\n";
            print $exception->getTraceAsString();
            print '</pre>';
        } else {
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
                $storeManager = $this->_objectManager->get('Magento\Core\Model\StoreManager');
                $reportData['skin'] = $storeManager->getStore()->getCode();
            } catch (\Exception $exception) {
            }

            $modelDir = $this->_objectManager->get('Magento\App\Dir');
            require_once($modelDir->getDir(\Magento\App\Dir::PUB) . DS . 'errors' . DS . 'report.php');
        }
    }

    /**
     * Initializes the entry point, so a Magento application is ready to be used
     */
    protected function _init()
    {
        if ($this->_objectManager) {
            return ;
        }
        $directories = new Dir(
            $this->_rootDir,
            isset($this->_parameters[Dir::PARAM_APP_DIRS]) ? $this->_parameters[Dir::PARAM_APP_DIRS] : array(),
            isset($this->_parameters[Dir::PARAM_APP_URIS]) ? $this->_parameters[Dir::PARAM_APP_URIS] : array()
        );

        \Magento\Autoload\IncludePath::addIncludePath(array($directories->getDir(Dir::GENERATION)));

        $options = new Options(
            $this->_parameters,
            new Options\Loader(
                $directories,
                isset($this->_parameters[Options\Loader::PARAM_CUSTOM_FILE])
                    ? $this->_parameters[Options\Loader::PARAM_CUSTOM_FILE]
                    : null
            )
        );

        $definitionFactory = new \Magento\ObjectManager\DefinitionFactory(
            $directories->getDir(DIR::DI),
            $directories->getDir(DIR::GENERATION),
            $options->get('definition.format', 'serialized')
        );

        $definitions = $definitionFactory->createClassDefinition($options->get('definitions'));
        $relations = $definitionFactory->createRelations();
        $diConfig = new Config($relations, $definitions);
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

        $this->_objectManager = new ObjectManager($factory, $diConfig, array(
            'Magento\App\Config' => $options,
            'Magento\App\Dir' => $directories
        ));
        \Magento\App\ObjectManager::setInstance($this->_objectManager);

        $verification = $this->_objectManager->get('Magento\App\Dir\Verification');
        $verification->createAndVerifyDirectories();

        $diConfig->setCache($this->_objectManager->get('Magento\App\ObjectManager\ConfigCache'));
        $this->_objectManager->configure(
            $this->_objectManager->get('Magento\App\ObjectManager\ConfigLoader')->load('global')
        );

        $relations = $definitionFactory->createRelations();

        $interceptionConfig = $this->_objectManager->create('Magento\Interception\Config\Config', array(
            'relations' => $relations,
            'omConfig' => $diConfig,
            'classDefinitions' => $definitions instanceof \Magento\ObjectManager\Definition\Compiled
                    ? $definitions
                    : null,
        ));

        $pluginList = $this->_objectManager->create('Magento\Interception\PluginList\PluginList', array(
            'relations' => $relations,
            'definitions' => $definitionFactory->createPluginDefinition(),
            'omConfig' => $diConfig,
            'classDefinitions' => $definitions instanceof \Magento\ObjectManager\Definition\Compiled
                    ? $definitions
                    : null,
        ));
        $factory = $this->_objectManager->create('Magento\Interception\FactoryDecorator', array(
            'factory' => $factory,
            'config' => $interceptionConfig,
            'pluginList' => $pluginList
        ));
        $this->_objectManager->setFactory($factory);
        $this->_objectManager->get('Magento\Core\Model\Resource')
            ->setConfig($this->_objectManager->get('Magento\Core\Model\Config\Resource'));
    }

    /**
     * Template method to process request according to the actual entry point rules
     */
    protected abstract function _processRequest();
}

