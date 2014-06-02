<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Service;

use Zend\Di\Di;
use Zend\Di\Exception\ClassNotFoundException;
use Zend\Mvc\Exception\DomainException;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception;
use Zend\ServiceManager\ServiceLocatorInterface;

class DiStrictAbstractServiceFactory extends Di implements AbstractFactoryInterface
{
    /**@#+
     * constants
     */
    const USE_SL_BEFORE_DI = 'before';
    const USE_SL_AFTER_DI  = 'after';
    const USE_SL_NONE      = 'none';
    /**@#-*/

    /**
     * @var Di
     */
    protected $di = null;

    /**
     * @var string
     */
    protected $useServiceLocator = self::USE_SL_AFTER_DI;

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator = null;

    /**
     * @param Di $di
     * @param string $useServiceLocator
     */
    public function __construct(Di $di, $useServiceLocator = self::USE_SL_NONE)
    {
        $this->useServiceLocator = $useServiceLocator;
        // since we are using this in a proxy-fashion, localize state
        $this->di              = $di;
        $this->definitions     = $this->di->definitions;
        $this->instanceManager = $this->di->instanceManager;
    }

    /**
     * {@inheritDoc}
     *
     * Allows creation of services only when in a whitelist
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $serviceName, $requestedName)
    {
        if ($serviceLocator instanceof AbstractPluginManager) {
            /* @var $serviceLocator AbstractPluginManager */
            $this->serviceLocator = $serviceLocator->getServiceLocator();
        } else {
            $this->serviceLocator = $serviceLocator;
        }

        return parent::get($requestedName);
    }

    /**
     * Overrides Zend\Di to allow the given serviceLocator's services to be reused by Di itself
     *
     * {@inheritDoc}
     *
     * @throws Exception\InvalidServiceNameException
     */
    public function get($name, array $params = array())
    {
        if (null === $this->serviceLocator) {
            throw new DomainException('No ServiceLocator defined, use `createServiceWithName` instead of `get`');
        }

        if (self::USE_SL_BEFORE_DI === $this->useServiceLocator && $this->serviceLocator->has($name)) {
            return $this->serviceLocator->get($name);
        }

        try {
            return parent::get($name, $params);
        } catch (ClassNotFoundException $e) {
            if (self::USE_SL_AFTER_DI === $this->useServiceLocator && $this->serviceLocator->has($name)) {
                return $this->serviceLocator->get($name);
            }

            throw new Exception\ServiceNotFoundException(
                sprintf('Service %s was not found in this DI instance', $name),
                null,
                $e
            );
        }
    }

    /**
     * {@inheritDoc}
     *
     * Allows creation of services only when in a whitelist
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return true;
    }
}
