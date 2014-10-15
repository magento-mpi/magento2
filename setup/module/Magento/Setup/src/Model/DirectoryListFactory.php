<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Setup\Model;

use Zend\ServiceManager\ServiceLocatorInterface;
use Magento\Config\Config;
use Magento\Config\ConfigFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

class DirectoryListFactory
{
    /**
     * Zend Framework's service locator
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var Config
     */
    protected $config;

    /**
     * Constructor
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param ConfigFactory $configFactory
     */
    public function __construct(
        ServiceLocatorInterface $serviceLocator,
        ConfigFactory $configFactory
    ) {
        $this->serviceLocator = $serviceLocator;
        $this->config = $configFactory->create();
    }

    /**
     * Factory method for DirectoryList object
     *
     * @return DirectoryList
     */
    public function create()
    {
        return new DirectoryList(
            $this->config->getMagentoBasePath()
        );
    }
}
