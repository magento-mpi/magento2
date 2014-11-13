<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Module;

use Magento\Framework\App\Resource;
use Magento\Setup\Module\Setup\ResourceConfig;
use Zend\ServiceManager\ServiceLocatorInterface;

class ResourceFactory {
    /**
     * Zend Framework's service locator
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Constructor
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * @param \Magento\Framework\App\Arguments $arguments
     * @return Resource
     */
    public function create(\Magento\Framework\App\Arguments $arguments)
    {
        $connectionFactory = $this->serviceLocator->get('Magento\Framework\Model\Resource\Type\Db\ConnectionFactory');
        $resource = new Resource(
            new ResourceConfig,
            $connectionFactory,
            $arguments
        );
        return $resource;
    }
} 