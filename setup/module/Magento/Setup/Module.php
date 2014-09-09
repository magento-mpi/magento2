<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup;

use Zend\Console\Adapter\AdapterInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConsoleBannerProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventInterface;
use Magento\Setup\Mvc\View\Http\InjectTemplateListener;

class Module implements
    BootstrapListenerInterface,
    ConfigProviderInterface,
    AutoloaderProviderInterface,
    ConsoleBannerProviderInterface,
    ConsoleUsageProviderInterface
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

    /**
     * {@inheritdoc}
     */
    public function getConsoleBanner(AdapterInterface $console)
    {
        return
            "==------------------------------------------------------==\n" .
            "        Welcome to Magento 2.x Installation Wizard        \n" .
            "==------------------------------------------------------==\n"
            ;
    }


    /**
     * {@inheritdoc}
     */
    public function getConsoleUsage(AdapterInterface $console)
    {
        return [
            'List of Options',
            'show locales' => 'Show possible locales',
            'show currencies' => 'Show all acceptable currencies',
            'show timezones' => 'Show all timezones',
            'show options' => 'Show all install options',
            'Installation Commands',
            'install configuration [--<install_option_name> "<option_value>" ...]'
                => 'Installing deployment configuration file',
            ['license_agreement_accepted', 'yes'],
            ['db_host', 'localhost'],
            ['db_name', 'magento'],
            ['db_user', 'root'],
            ['admin_url', 'admin'],
            'Sample Deployment Configuration tool command: ',
            'php -f index.php install configuration --license_agreement_accepted yes --db_host localhost'
            . ' --db_name magentosetup --db_user root --admin_url "admin"',
            '***************************************************' . "\n",
            'install schema [--<install_option_name> "<option_value>" ...]' => 'Installing database schema',
            [   'magentoDir' , 'C:\wamp\www\magento2'],
            'Sample Schema Installer and Updater tool command: ',
            'php -f index.php install schema  --magentoDir "C:\wamp\www\magento2"',
            '***************************************************' . "\n",
            'install data [--<install_option_name> "<option_value>" ...]' => 'Installing data files',
            [   'magentoDir' , 'C:\wamp\www\magento2'],
            'Sample Data Installer and Updater tool command: ',
            'php -f index.php install data  --magentoDir "C:\wamp\www\magento2"',
            '***************************************************' . "\n",
        ];
    }
}
