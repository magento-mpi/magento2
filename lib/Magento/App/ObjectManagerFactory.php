<?php
/**
 * Initialize application object manager.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App;

use Magento\App\Dir,
    Magento\App\Config,
    Magento\ObjectManager\Factory\Factory,
    Magento\Profiler;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * Class ObjectManagerFactory
 * @package Magento\App
 */
class ObjectManagerFactory
{
    /**
     * Locator class name
     *
     * @var string
     */
    protected $_locatorClassName = '\Magento\ObjectManager\ObjectManager';

    /**
     * Config class name
     *
     * @var string
     */
    protected $_configClassName = '\Magento\ObjectManager\Config\Config';

    /**
     * Create object manager
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     *
     * @param string $rootDir
     * @param array $arguments
     * @return \Magento\ObjectManager\ObjectManager
     * @throws \Magento\BootstrapException
     */
    public function create($rootDir, array $arguments)
    {
        $directories = new Dir(
            $rootDir,
            isset($arguments[Dir::PARAM_APP_URIS]) ? $arguments[Dir::PARAM_APP_URIS] : array(),
            isset($arguments[Dir::PARAM_APP_DIRS]) ? $arguments[Dir::PARAM_APP_DIRS] : array()
        );

        \Magento\Autoload\IncludePath::addIncludePath(array($directories->getDir(Dir::GENERATION)));

        $options = new Config(
            $arguments,
            new Config\Loader(
                $directories,
                isset($arguments[Config\Loader::PARAM_CUSTOM_FILE])
                    ? $arguments[Config\Loader::PARAM_CUSTOM_FILE]
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
        $configClass = $this->_configClassName;
        /** @var \Magento\ObjectManager\Config\Config $diConfig */
        $diConfig = new $configClass($relations, $definitions);
        $appMode = $options->get(State::PARAM_MODE, State::MODE_DEFAULT);

        $configData = $this->_loadPrimaryConfig($directories, $appMode);

        if ($configData) {
            $diConfig->extend($configData);
        }

        $factory = new Factory($diConfig, null, $definitions, $options->get());

        $className = $this->_locatorClassName;
        /** @var \Magento\ObjectManager $locator */
        $locator = new $className($factory, $diConfig, array(
            'Magento\App\Config' => $options,
            'Magento\App\Dir' => $directories
        ));

        \Magento\App\ObjectManager::setInstance($locator); 

        /** @var \Magento\App\Dir\Verification $verification */
        $verification = $locator->get('Magento\App\Dir\Verification');
        $verification->createAndVerifyDirectories();

        $diConfig->setCache($locator->get('Magento\App\ObjectManager\ConfigCache'));
        $locator->configure(
            $locator->get('Magento\App\ObjectManager\ConfigLoader')->load('global')
        );
        $locator->get('Magento\Config\ScopeInterface')->setCurrentScope('global');
        $locator->get('Magento\App\Resource')->setCache($locator->get('Magento\App\CacheInterface'));

        $relations = $definitionFactory->createRelations();

        $interceptionConfig = $locator->create('Magento\Interception\Config\Config', array(
            'relations' => $relations,
            'omConfig' => $diConfig,
            'classDefinitions' => $definitions instanceof \Magento\ObjectManager\Definition\Compiled
                ? $definitions
                : null,
        ));

        $pluginList = $locator->create('Magento\Interception\PluginList\PluginList', array(
            'relations' => $relations,
            'definitions' => $definitionFactory->createPluginDefinition(),
            'omConfig' => $diConfig,
            'classDefinitions' => $definitions instanceof \Magento\ObjectManager\Definition\Compiled
                ? $definitions
                : null,
        ));
        $factory = $locator->create('Magento\Interception\FactoryDecorator', array(
            'factory' => $factory,
            'config' => $interceptionConfig,
            'pluginList' => $pluginList
        ));
        $locator->setFactory($factory);
        return $locator;
    }

    /**
     * Load primary config data
     *
     * @param Dir $directories
     * @param string $appMode
     * @return array
     * @throws \Magento\BootstrapException
     */
    protected function _loadPrimaryConfig(Dir $directories, $appMode)
    {
        $configData = null;
        $primaryLoader = new \Magento\App\ObjectManager\ConfigLoader\Primary($directories, $appMode);
        try {
            $configData = $primaryLoader->load();
        } catch (\Exception $e) {
            throw new \Magento\BootstrapException($e->getMessage());
        }
        return $configData;
    }
}