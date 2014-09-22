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
use Magento\Setup\Mvc\View\Http\InjectTemplateListener;

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
        /** @var \Zend\Mvc\MvcEvent $e */
        /** @var \Zend\Mvc\Application $application */
        $application = $e->getApplication();
        /** @var \Zend\EventManager\EventManager $events */
        $events = $application->getEventManager();
        /** @var \Zend\EventManager\SharedEventManager $sharedEvents */
        $sharedEvents = $events->getSharedManager();

        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($events);

        // Override Zend\Mvc\View\Http\InjectTemplateListener
        // to process templates by Vendor/Module
        $injectTemplateListener = new InjectTemplateListener();
        $translator = $application->getServiceManager()->get('translator');
        $sharedEvents->attach(
            'Zend\Stdlib\DispatchableInterface',
            MvcEvent::EVENT_DISPATCH,
            [
                new \Magento\Setup\Model\Location($translator),
                'onChangeLocation'
            ],
            10
        );
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
            include __DIR__ . '/config/di.config.php',
            include __DIR__ . '/config/states.config.php',
            include __DIR__ . '/config/languages.config.php'
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
