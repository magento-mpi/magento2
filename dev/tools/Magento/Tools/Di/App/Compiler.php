<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\Di\App;

use Magento\Framework\App;
use Zend\Code\Scanner\FileScanner;
use Magento\Tools\Di\Code\Scanner;
use Magento\Tools\Di\Definition\Collection as DefinitionsCollection;
use Magento\Tools\Di\Compiler\ArgumentsResolverFactory;
use Magento\Tools\Di\Code\Reader\ClassReaderDecorator;
use Magento\Tools\Di\Code\Reader\ClassesScanner;
use Magento\Tools\Di\Code\Generator\InterceptionConfigurationBuilder;

class Compiler implements \Magento\Framework\AppInterface
{
    /**
     * @var \Magento\Framework\ObjectManager\ConfigInterface
     */
    private $diContainerConfig;

    /**
     * @var App\AreaList
     */
    private $areaList;

    /**
     * @var App\ObjectManager\ConfigLoader
     */
    private $configLoader;

    /**
     * @var ArgumentsResolverFactory
     */
    private $argumentsResolverFactory;

    /**
     * @var ClassReaderDecorator
     */
    private $classReaderDecorator;

    /**
     * @var ClassesScanner
     */
    private $classesScanner;

    /**
     * @var InterceptionConfigurationBuilder
     */
    private $interceptionConfigurationBuilder;

    /**
     * @var array
     */
    protected $assertions = [
        'adminhtml.ser' => 'e576720abbc6538849772ffee9a5bd89',
        'global.ser' => '571698d6bdecc3b5126cb7b01c77a764',
        'doc.ser' => 'ca8cb5088a510f41f13e66750b1fb218',
        'frontend.ser' => '070f9374c11725293cbeb6d3e8a5717b',
        'webapi_rest.ser' => 'ce0c4e6af2a7cad9b5b52c2edbf17d11',
        'webapi_soap.ser' => '5f05ef085c27269854e456a3d422f4fd'
    ];

    /**
     * @param \Magento\Framework\ObjectManager\ConfigInterface $diContainerConfig
     * @param App\AreaList $areaList
     * @param App\ObjectManager\ConfigLoader $configLoader
     * @param ArgumentsResolverFactory $argumentsResolverFactory
     * @param ClassReaderDecorator $classReaderDecorator
     * @param ClassesScanner $classesScanner
     * @param InterceptionConfigurationBuilder $interceptionConfigurationBuilder
     */
    public function __construct(
        \Magento\Framework\ObjectManager\ConfigInterface $diContainerConfig,
        App\AreaList $areaList,
        App\ObjectManager\ConfigLoader $configLoader,
        ArgumentsResolverFactory $argumentsResolverFactory,
        ClassReaderDecorator $classReaderDecorator,
        ClassesScanner $classesScanner,
        InterceptionConfigurationBuilder $interceptionConfigurationBuilder
    ) {
        $this->diContainerConfig = $diContainerConfig;
        $this->areaList = $areaList;
        $this->configLoader = $configLoader;
        $this->argumentsResolverFactory = $argumentsResolverFactory;
        $this->classReaderDecorator = $classReaderDecorator;
        $this->classesScanner = $classesScanner;
        $this->interceptionConfigurationBuilder = $interceptionConfigurationBuilder;
    }

    /**
     * Launch application
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function launch()
    {
        //$this->storeInterceptedMethods();
        $paths = ['app/code', 'lib/internal/Magento/Framework', 'var/generation'];
        $definitionsCollection = new DefinitionsCollection;
        foreach ($paths as $path) {
            $definitionsCollection->addCollection($this->getDefinitionsCollection(BP . '/' . $path));
        }
        if (!file_exists(BP . '/var/di')) {
            mkdir(BP . '/var/di');
        }
        $this->generateCachePerScope($definitionsCollection, 'global');
        $this->interceptionConfigurationBuilder->addAreaCode('global');
        foreach ($this->areaList->getCodes() as $areaCode) {
            $this->interceptionConfigurationBuilder->addAreaCode($areaCode);
            $this->generateCachePerScope($definitionsCollection, $areaCode, true);
        }

        $generatorIo = new \Magento\Framework\Code\Generator\Io(
            new \Magento\Framework\Filesystem\Driver\File(),
            BP . '/var/generation'
        );
        $generator = new \Magento\Tools\Di\Code\Generator(
            $generatorIo,
            array(
                \Magento\Framework\Interception\Code\Generator\Interceptor::ENTITY_TYPE =>
                    'Magento\Tools\Di\Code\Generator\Interceptor',
            )
        );
        $interceptionConfiguration = $this->interceptionConfigurationBuilder->getInterceptionConfiguration();
        $generator->generateList($interceptionConfiguration);

        $response = new \Magento\Framework\App\Console\Response();
        $response->setCode(0);
        return $response;
    }

    /**
     * Ability to handle exceptions that may have occurred during bootstrap and launch
     *
     * Return values:
     * - true: exception has been handled, no additional action is needed
     * - false: exception has not been handled - pass the control to Bootstrap
     *
     * @param App\Bootstrap $bootstrap
     * @param \Exception $exception
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @return bool
     */
    public function catchException(App\Bootstrap $bootstrap, \Exception $exception)
    {
        return false;
    }

    /**
     * @param string $fileName
     *
     * @return void
     */
    private function assertMd5($fileName)
    {
        echo ($this->assertions[$fileName] ==
            md5_file(BP . '/var/di/' . $fileName) ? '. ' : 'Failed for '. $fileName . ' ');
    }

    /**
     * @param DefinitionsCollection $definitionsCollection
     * @param string $areaCode
     * @param bool $extendConfig
     *
     * @return void
     */
    private function generateCachePerScope(
        DefinitionsCollection $definitionsCollection,
        $areaCode,
        $extendConfig = false
    ) {
        $areaConfig = clone $this->diContainerConfig;
        if ($extendConfig) {
            $areaConfig->extend($this->configLoader->load($areaCode));
        }

        $config = [];
        $config['arguments'] = $this->getConfigForScope($definitionsCollection, $areaConfig);
        foreach ($definitionsCollection->getInstancesNamesList() as $instanceName) {
            if (!$areaConfig->isShared($instanceName)) {
                $config['nonShared'][$instanceName] = true;
            }
            $preference = $areaConfig->getPreference($instanceName);
            if ($instanceName !== $preference) {
                $config['preferences'][$instanceName] = $preference;
            }
        }
        foreach (array_keys($areaConfig->getVirtualTypes()) as $virtualType) {
            $config['instanceTypes'][$virtualType] = $areaConfig->getInstanceType($virtualType);
        }
        $serialized = serialize($config);
        file_put_contents(BP . '/var/di/' . $areaCode . '.ser', $serialized);
        $this->assertMd5($areaCode . '.ser');
    }


    /**
     * Returns definitions collection
     *
     * @param string $path
     * @return DefinitionsCollection
     */
    protected function getDefinitionsCollection($path)
    {
        $definitions = new DefinitionsCollection;
        foreach ($this->classesScanner->getList($path) as $className => $constructorArguments) {
            $definitions->addDefinition($className, $constructorArguments);
        }
        return $definitions;
    }

    /**
     * @param DefinitionsCollection $definitionsCollection
     * @param \Magento\Framework\ObjectManager\ConfigInterface $config
     * @return array|mixed
     * @throws \ReflectionException
     */
    protected function getConfigForScope($definitionsCollection, $config)
    {
        $arguments = array();
        $argumentsResolver = $this->argumentsResolverFactory->create($config);
        foreach ($definitionsCollection->getInstancesNamesList() as $instanceType) {
            $refl = new \ReflectionClass($instanceType);
            if ($refl->isInterface() || $refl->isAbstract()) {
                continue;
            }
            $constructor = $definitionsCollection->getInstanceArguments($instanceType);
            $arguments[$instanceType] = $argumentsResolver->getResolvedConstructorArguments(
                $instanceType,
                $constructor
            );
        }
        foreach (array_keys($config->getVirtualTypes()) as $instanceType) {
            $originalType = $config->getInstanceType($instanceType);
            if (!$definitionsCollection->hasInstance($originalType)) {
                $refl = new \ReflectionClass($originalType);
                if ($refl->isInterface() || $refl->isAbstract()) {
                    continue;
                }
                $constructor = $this->classReaderDecorator->getConstructor($originalType);
            } else {
                $constructor = $definitionsCollection->getInstanceArguments($originalType);
            }
            $arguments[$instanceType] = $argumentsResolver->getResolvedConstructorArguments(
                $instanceType,
                $constructor
            );
        }
        return $arguments;
    }
}
