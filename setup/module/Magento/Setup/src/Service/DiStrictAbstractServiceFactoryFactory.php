<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mvc\Service\DiStrictAbstractServiceFactoryFactory as ZendDiStrictAbstractServiceFactoryFactory;

class DiStrictAbstractServiceFactoryFactory extends ZendDiStrictAbstractServiceFactoryFactory
{
    /**
     * Class responsible for instantiating a DiStrictAbstractServiceFactory
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return DiStrictAbstractServiceFactory
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $diAbstractFactory = new DiStrictAbstractServiceFactory(
            $serviceLocator->get('Di'),
            DiStrictAbstractServiceFactory::USE_SL_BEFORE_DI
        );

        return $diAbstractFactory;
    }
}
