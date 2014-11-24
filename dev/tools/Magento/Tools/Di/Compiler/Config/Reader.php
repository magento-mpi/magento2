<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Di\Compiler\Config;

use Magento\Framework\App;
use Magento\Tools\Di\Code\Reader\ClassReaderDecorator;
use Magento\Tools\Di\Compiler\ArgumentsResolverFactory;
use Magento\Tools\Di\Definition\Collection as DefinitionsCollection;

class Reader
{
    /**
     * @var \Magento\Framework\ObjectManager\ConfigInterface
     */
    private $diContainerConfig;

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
     * @var Writer\Filesystem
     */
    private $configWriter;

    /**
     * @param \Magento\Framework\ObjectManager\ConfigInterface $diContainerConfig
     * @param App\ObjectManager\ConfigLoader $configLoader
     * @param ArgumentsResolverFactory $argumentsResolverFactory
     * @param ClassReaderDecorator $classReaderDecorator
     * @param Writer\Filesystem $configWriter
     */
    public function __construct(
        \Magento\Framework\ObjectManager\ConfigInterface $diContainerConfig,
        App\ObjectManager\ConfigLoader $configLoader,
        ArgumentsResolverFactory $argumentsResolverFactory,
        ClassReaderDecorator $classReaderDecorator,
        Writer\Filesystem $configWriter
    ) {
        $this->diContainerConfig = $diContainerConfig;
        $this->configLoader = $configLoader;
        $this->argumentsResolverFactory = $argumentsResolverFactory;
        $this->classReaderDecorator = $classReaderDecorator;
        $this->configWriter = $configWriter;
    }

    /**
     * Generates config per scope and writes it to some storage
     *
     * @param DefinitionsCollection $definitionsCollection
     * @param string $areaCode
     * @param bool $extendConfig
     *
     * @return void
     */
    public function generateCachePerScope(
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

        $this->configWriter->write($areaCode, $config);
    }

    /**
     * Returns constructor with defined arguments
     *
     * @param DefinitionsCollection $definitionsCollection
     * @param \Magento\Framework\ObjectManager\ConfigInterface $config
     * @return array|mixed
     * @throws \ReflectionException
     */
    private function getConfigForScope(DefinitionsCollection $definitionsCollection, $config)
    {
        $constructors = array();
        $argumentsResolver = $this->argumentsResolverFactory->create($config);
        foreach ($definitionsCollection->getInstancesNamesList() as $instanceType) {
            $refl = new \ReflectionClass($instanceType);
            if ($refl->isInterface() || $refl->isAbstract()) {
                continue;
            }
            $constructor = $definitionsCollection->getInstanceArguments($instanceType);
            $constructors[$instanceType] = $argumentsResolver->getResolvedConstructorArguments(
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
            $constructors[$instanceType] = $argumentsResolver->getResolvedConstructorArguments(
                $instanceType,
                $constructor
            );
        }
        return $constructors;
    }
}
