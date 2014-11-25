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
use Magento\Tools\Di\Definition\Collection as DefinitionsCollection;
use Magento\Tools\Di\Compiler\Config;
use Magento\Tools\Di\Code\Reader\ClassesScanner;
use Magento\Tools\Di\Code\Generator\InterceptionConfigurationBuilder;

/**
 * Class Compiler
 * @package Magento\Tools\Di\App
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Compiler implements \Magento\Framework\AppInterface
{
    /**
     * @var App\AreaList
     */
    private $areaList;

    /**
     * @var ClassesScanner
     */
    private $classesScanner;

    /**
     * @var InterceptionConfigurationBuilder
     */
    private $interceptionConfigurationBuilder;

    /**
     * @var Config\Reader
     */
    private $configReader;

    /**
     * @var Config\Writer\Filesystem
     */
    private $configWriter;

    /**
     * @param App\AreaList $areaList
     * @param ClassesScanner $classesScanner
     * @param InterceptionConfigurationBuilder $interceptionConfigurationBuilder
     * @param Config\Reader $configReader
     * @param Config\Writer\Filesystem $configWriter
     */
    public function __construct(
        App\AreaList $areaList,
        ClassesScanner $classesScanner,
        InterceptionConfigurationBuilder $interceptionConfigurationBuilder,
        Config\Reader $configReader,
        Config\Writer\Filesystem $configWriter
    ) {
        $this->areaList = $areaList;
        $this->classesScanner = $classesScanner;
        $this->interceptionConfigurationBuilder = $interceptionConfigurationBuilder;
        $this->configReader = $configReader;
        $this->configWriter = $configWriter;
    }

    /**
     * Launch application
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function launch()
    {
        $paths = ['app/code', 'lib/internal/Magento/Framework', 'var/generation'];
        $definitionsCollection = new DefinitionsCollection;
        foreach ($paths as $path) {
            $definitionsCollection->addCollection($this->getDefinitionsCollection(BP . '/' . $path));
        }

        $this->configWriter->write(
            'global',
            $this->configReader->generateCachePerScope($definitionsCollection, 'global')
        );
        $this->interceptionConfigurationBuilder->addAreaCode('global');
        foreach ($this->areaList->getCodes() as $areaCode) {
            $this->interceptionConfigurationBuilder->addAreaCode($areaCode);
            $this->configWriter->write(
                $areaCode,
                $this->configReader->generateCachePerScope($definitionsCollection, $areaCode, true)
            );
        }

        $this->generateInterceptors();

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
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function catchException(App\Bootstrap $bootstrap, \Exception $exception)
    {
        return false;
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
     * Creates interceptors configuration and generates code
     *
     * @return void
     */
    private function generateInterceptors()
    {
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
        $configuration = $this->interceptionConfigurationBuilder->getInterceptionConfiguration(get_declared_classes());
        $generator->generateList($configuration);
    }
}
