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
use Magento\Tools\Di\Compiler\Log\Log;

class Compiler implements \Magento\Framework\AppInterface
{
    /**
     * @var \Magento\Framework\ObjectManager\Config
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
     * @todo introduce log
     * @var Log
     */
    private $log;

    protected $assertions = [
        'adminhtml.ser' => 'e576720abbc6538849772ffee9a5bd89',
        'global.ser' => '571698d6bdecc3b5126cb7b01c77a764',
        'doc.ser' => 'ca8cb5088a510f41f13e66750b1fb218',
        'frontend.ser' => '070f9374c11725293cbeb6d3e8a5717b',
        'webapi_rest.ser' => 'ce0c4e6af2a7cad9b5b52c2edbf17d11',
        'webapi_soap.ser' => '5f05ef085c27269854e456a3d422f4fd'
    ];

    /**
     * @param \Magento\Framework\ObjectManager\Config $diContainerConfig
     * @param App\AreaList $areaList
     * @param App\ObjectManager\ConfigLoader $configLoader
     * @param ArgumentsResolverFactory $argumentsResolverFactory
     * @param ClassReaderDecorator $classReaderDecorator
     */
    public function __construct(
        \Magento\Framework\ObjectManager\Config $diContainerConfig,
        App\AreaList $areaList,
        App\ObjectManager\ConfigLoader $configLoader,
        ArgumentsResolverFactory $argumentsResolverFactory,
        ClassReaderDecorator $classReaderDecorator
    ) {
        $this->diContainerConfig = $diContainerConfig;
        $this->areaList = $areaList;
        $this->configLoader = $configLoader;
        $this->argumentsResolverFactory = $argumentsResolverFactory;
        $this->classReaderDecorator = $classReaderDecorator;
    }

    /**
     * Returns definitions collection
     *
     * @param string $path
     * @return DefinitionsCollection
     */
    protected function getClasses($path)
    {
        $recursiveIterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(realpath($path)), 1);
        $definitions = new DefinitionsCollection;
        /** @var $fileItem \SplFileInfo */
        foreach ($recursiveIterator as $fileItem) {
            if (!$this->isPhpFile($fileItem)) {
                continue;
            }
            $fileScanner = new FileScanner($fileItem->getRealPath());
            $classNames = $fileScanner->getClassNames();
            foreach ($classNames as $className) {
                if (!class_exists($className)) {
                    require_once $fileItem->getRealPath();
                }
                try {
                    $definitions->addDefinition($className, $this->classReaderDecorator->getConstructor($className));
                } catch (\Magento\Framework\Code\ValidationException $exception) {
                    $this->log->add(Log::COMPILATION_ERROR, $className, $exception->getMessage());
                } catch (\ReflectionException $e) {
                    $this->log->add(Log::COMPILATION_ERROR, $className, $e->getMessage());
                }
            }
        }
        return $definitions;
    }

    /**
     * @param DefinitionsCollection $definitionsCollection
     * @param \Magento\Framework\ObjectManager\Config $config
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
            $definitionsCollection->addCollection($this->getClasses(BP . '/' . $path));
        }
        if (!file_exists(BP . '/var/di')) {
            mkdir(BP . '/var/di');
        }

        $this->generateCachePerScope($definitionsCollection, 'global');

        foreach ($this->areaList->getCodes() as $areaCode) {
            $this->generateCachePerScope($definitionsCollection, $areaCode, true);
        }
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
     */
    public function catchException(App\Bootstrap $bootstrap, \Exception $exception)
    {
        return true;
    }

    private function storeInterceptedMethods()
    {
        $filePatterns = array('di' => '/\/etc\/([a-zA-Z_]*\/di|di)\.xml$/');
        $codeScanDir = BP . '/app';
        $directoryScanner = new Scanner\DirectoryScanner();
        $files = $directoryScanner->scan($codeScanDir, $filePatterns);
        $files['di'] = isset($files['di']) && is_array($files['di']) ? $files['di'] : array();

        $scanner = new Scanner\CompositeScanner();
        $scanner->addChild(new Scanner\InterceptedInstancesScanner(), 'di');

        $interceptedInstances = $scanner->collectEntities($files);
        $pluginDefinitionList = new \Magento\Framework\Interception\Definition\Runtime();
        $pluginDefinitions = array();
        foreach ($interceptedInstances as $type => $entityList) {
            foreach ($entityList as $entity) {
                $pluginDefinitions[$entity] = $pluginDefinitionList->getMethodList($entity);
            }
        }
        mkdir(BP . '/var/di');
        file_put_contents(BP . '/var/di/' . 'plugin_definitions.ser', serialize($pluginDefinitions));
    }

    private function assertMd5($fileName)
    {
        echo ($this->assertions[$fileName] == md5_file(BP . '/var/di/' . $fileName) ? '. ' : 'Failed for '. $fileName . ' ');
    }

    /**
     * @param DefinitionsCollection$definitionsCollection
     * @param string $areaCode
     * @param bool $extendConfig
     */
    private function generateCachePerScope($definitionsCollection, $areaCode, $extendConfig = false)
    {
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
     * Whether file is .php file
     *
     * @param \SplFileInfo $item
     * @return bool
     */
    private function isPhpFile(\SplFileInfo $item)
    {
        return $item->isFile() && pathinfo($item->getRealPath(), PATHINFO_EXTENSION) == 'php';
    }
}
