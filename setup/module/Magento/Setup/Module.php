<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup;

use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventInterface;
use Magento\Setup\View\Http\InjectTemplateListener;

class Module implements
    BootstrapListenerInterface,
    ConfigProviderInterface,
    AutoloaderProviderInterface
{
    /**
     * @param EventInterface $e
     * @return void
     */
    public function onBootstrap(EventInterface $e)
    {
        $application = $e->getApplication();
        $events = $application->getEventManager();
        $sharedEvents = $events->getSharedManager();

        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($events);

        // Override Zend\Mvc\View\Http\InjectTemplateListener
        // to process templates by Vendor/Module
        $injectTemplateListener = new InjectTemplateListener();
        $sharedEvents->attach(
            'Zend\Stdlib\DispatchableInterface',
            MvcEvent::EVENT_DISPATCH,
            [$injectTemplateListener, 'injectTemplate'],
            -89
        );
    }

    /**
     * @return array|mixed|\Traversable
     */
    public function getConfig()
    {
        return array_merge(
            include __DIR__ . '/config/module.config.php',
            include __DIR__ . '/config/router.config.php',
            include __DIR__ . '/config/di.config.php'
        );
    }

    /**
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/',
                ),
            ),
        );
    }
}
