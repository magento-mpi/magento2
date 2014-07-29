<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Config;

use Zend\Filter\Inflector;
use Zend\ServiceManager\ServiceLocatorInterface;

class ConfigFactory
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var array
     */
    protected $configuration = [];

    /**
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        $this->configuration = $this->serviceLocator->get('config')['parameters'];
    }

    /**
     * @return Config
     */
    public function create()
    {
        return new Config(new Inflector(), $this->configuration);
    }
}
