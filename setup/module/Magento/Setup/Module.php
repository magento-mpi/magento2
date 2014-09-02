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
     * Returns a string containing a banner text, that describes the module and/or the application.
     * The banner is shown in the console window, when the user supplies invalid command-line parameters or invokes
     * the application with no parameters.
     *
     * The method is called with active Zend\Console\Adapter\AdapterInterface that can be used to directly access Console and send
     * output.
     *
     * @param AdapterInterface $console
     * @return string|null
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
     * Returns an array or a string containing usage information for this module's Console commands.
     * The method is called with active Zend\Console\Adapter\AdapterInterface that can be used to directly access
     * Console and send output.
     *
     * If the result is a string it will be shown directly in the console window.
     * If the result is an array, its contents will be formatted to console window width. The array must
     * have the following format:
     *
     *     return array(
     *                'Usage information line that should be shown as-is',
     *                'Another line of usage info',
     *
     *                '--parameter'        =>   'A short description of that parameter',
     *                '-another-parameter' =>   'A short description of another parameter',
     *                ...
     *            )
     *
     * @param AdapterInterface $console
     * @return array|string|null
     */
    public function getConsoleUsage(AdapterInterface $console)
    {
        return [
            'Retrieving List of Options',
            'show locales'              => 'Show possible locales',
            'show currencies'               => 'Show all acceptable currencies',
            'show timezones'                => 'Show all timezones',
            'show options'              => 'Show all install options',
            'Command Line Options',
            'install local [--<install_option_name> "<option_value>" ...]'              => 'Installing Local.xml file',
            [   'license_agreement_accepted' , 'yes' ],
            [   'db_host' , 'localhost'],
            [   'db_name' , 'magento'],
            [   'db_user' , 'root'],
            [   'store_url' , 'http://magento.local/'],
            [   'admin_url' , 'http://magento.local/admin'],
            [   'secure_store_url' , 'yes'],
            [   'secure_admin_url' , 'yes'],
            [   'use_rewrites' , 'no'],
            [   'locale' , 'en_US'],
            [   'timezone' , 'America/Los_Angeles'],
            [   'currency' , 'USD'],
            [   'admin_lastname' , 'Smith'],
            [   'admin_firstname' , 'John'],
            [   'admin_email' , 'john.smith@some-email.com'],
            [   'admin_username' , 'admin'],
            [   'admin_password' , '1234qasd'],
            'Example of installation: ',
            'php -f index.php install local --license_agreement_accepted yes --db_host localhost' .
            ' --db_name magentosetup --db_user root --store_url "http://127.0.0.1/"' .
            ' --admin_url "http://127.0.0.1/admin" --secure_store_url yes --locale "en_US"' .
            ' --timezone "America/Los_Angeles" --currency "USD" --admin_lastname Smith --admin_firstname John' .
            ' --admin_email 123@gmail.com --admin_username "admin" --admin_password "123123q"',
            'install data'          => 'Installs Data files'
        ];
    }
}
