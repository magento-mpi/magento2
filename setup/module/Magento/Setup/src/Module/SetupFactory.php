<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Module;

use Magento\Framework\App\Resource;
use Zend\ServiceManager\ServiceLocatorInterface;
use Magento\Setup\Model\LoggerInterface;

class SetupFactory
{
    /**
     * @var ServiceLocatorInterface
     */
    private $serviceLocator;

    /**
     * @var ResourceFactory
     */
    private $resourceFactory;
    /**
     * @var \Magento\Framework\App\Arguments\Loader
     */
    private $configLoader;

    /**
     * Constructor
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param \Magento\Framework\App\Arguments\Loader $configLoader
     * @param ResourceFactory $resourceFactory
     */
    public function __construct(
        ServiceLocatorInterface $serviceLocator,
        \Magento\Framework\App\Arguments\Loader $configLoader,
        \Magento\Setup\Module\ResourceFactory $resourceFactory
    ) {
        $this->serviceLocator = $serviceLocator;
        $this->resourceFactory = $resourceFactory;
        $this->configLoader = $configLoader;
    }

    /**
     * Creates Setup
     *
     * @return Setup
     */
    public function createSetup()
    {
        return new Setup($this->getResource());
    }

    /**
     * Creates SetupModule
     *
     * @param LoggerInterface $log
     * @param string $moduleName
     * @return SetupModule
     */
    public function createSetupModule(LoggerInterface $log, $moduleName)
    {
        return new SetupModule(
            $log,
            $this->serviceLocator->get('Magento\Framework\Module\ModuleList'),
            $this->serviceLocator->get('Magento\Setup\Module\Setup\FileResolver'),
            $moduleName,
            $this->getResource()
        );
    }

    private function getResource()
    {
        $arguments = new \Magento\Framework\App\Arguments(
            [],
            $this->configLoader
        );
        return $this->resourceFactory->create($arguments);
    }
}
