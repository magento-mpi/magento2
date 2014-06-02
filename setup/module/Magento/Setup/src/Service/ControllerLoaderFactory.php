<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Service;

use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mvc\Service\ControllerLoaderFactory as ZendControllerLoaderFactory;

class ControllerLoaderFactory extends ZendControllerLoaderFactory
{
    /**
     * Create the controller loader service
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return ControllerManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $controllerLoader = new ControllerManager();
        $controllerLoader->setServiceLocator($serviceLocator);
        $controllerLoader->addPeeringServiceManager($serviceLocator);

        if ($serviceLocator->has('Di')) {
            $controllerLoader->addAbstractFactory($serviceLocator->get('DiStrictAbstractServiceFactory'));
        }

        return $controllerLoader;
    }
}
