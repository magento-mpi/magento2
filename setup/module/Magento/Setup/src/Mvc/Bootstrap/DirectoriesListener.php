<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Setup\Mvc\Bootstrap;

use Magento\Framework\Filesystem;
use Magento\Framework\App\Bootstrap as AppBootstrap;
use Magento\Framework\App\Filesystem\DirectoryList;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Application;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;
use Zend\Console\Request;
use Zend\Stdlib\RequestInterface;

/**
 * A listener that injects custom directory paths and initializes Filesystem component for Zend application
 */
class DirectoriesListener implements ListenerAggregateInterface
{
    /**
     * List of ZF event listeners
     *
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    private $listeners = array();

    /**
     * Registers itself to every command in console routes
     *
     * @param array &$config
     * @return void
     */
    public static function registerCliRoutes(&$config)
    {
        foreach ($config['console']['router']['routes'] as &$route) {
            $route['options']['route'] .= ' [--' . AppBootstrap::INIT_PARAM_FILESYSTEM_DIR_PATHS . '=]';
        }
    }

    /**
     * Adds itself to CLI usage instructions
     *
     * @param $config
     */
    public static function registerCliUsage(&$config)
    {
        $config[] = '';
        $config[] = [
            '[--' . AppBootstrap::INIT_PARAM_FILESYSTEM_DIR_PATHS . sprintf('=%s]', escapeshellarg('<query>')),
            'Add to any command to customize Magento filesystem paths in bootstrap'
        ];
        $config[] = [
            '',
            sprintf('For example: %s', escapeshellarg('base[path]=/var/www/example.com&cache[path]=/var/tmp/cache'))
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $sharedEvents = $events->getSharedManager();
        $this->listeners[] = $sharedEvents->attach(
            'Zend\Mvc\Application',
            MvcEvent::EVENT_BOOTSTRAP,
            array($this, 'onBootstrap')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * An event subscriber that initializes DirectoryList and Filesystem objects in ZF application bootstrap
     *
     * @param MvcEvent $e
     * @return void
     */
    public function onBootstrap(\Zend\Mvc\MvcEvent $e)
    {
        /** @var Application $application */
        $application = $e->getApplication();
        $directoryList = $this->createDirectoryList($this->getDirectoryListConfig($application));
        $serviceManager = $application->getServiceManager();
        $serviceManager->setService('Magento\Framework\App\Filesystem\DirectoryList', $directoryList);
        $serviceManager->setService('Magento\Framework\Filesystem', $this->createFilesystem($directoryList));

    }

    /**
     * Collects DirectoryList configuration from multiple sources
     *
     * Each next step overwrites previous, whenever data is available, in the following order:
     * 1: ZF application config
     * 2: environment
     * 3: CLI parameters (if the application is running in CLI mode)
     *
     * @param Application $application
     * @return array
     */
    private function getDirectoryListConfig(Application $application)
    {
        $result = array_replace_recursive(
            $this->extractInitParam($application->getConfig()),
            $this->extractInitParam($_SERVER),
            $this->extractFromCli($application->getRequest())
        );
        DirectoryList::validate($result);
        return $result;
    }

    /**
     * Extracts the directory paths init parameter from an array
     *
     * @param array $config
     * @return array
     */
    private function extractInitParam($config)
    {
        $initKey = AppBootstrap::INIT_PARAM_FILESYSTEM_DIR_PATHS;
        if (isset($config[$initKey])) {
            return $config[$initKey];
        }
        return [];
    }

    /**
     * Extracts the directory paths from a CLI request
     *
     * Uses format of a URL query
     *
     * @param RequestInterface $request
     * @return array
     */
    private function extractFromCli(RequestInterface $request)
    {
        if (!($request instanceof Request)) {
            return [];
        }
        $initKey = AppBootstrap::INIT_PARAM_FILESYSTEM_DIR_PATHS;
        $result = [];
        foreach ($request->getContent() as $paramStr) {
            if (preg_match('/^\-\-' . preg_quote($initKey, '/') . '=(.+)$/', $paramStr, $matches)) {
                parse_str($matches[1], $result);
            }
        }
        DirectoryList::validate($result);
        return $result;
    }

    /**
     * Initializes DirectoryList service
     *
     * @param array $config
     * @return DirectoryList
     * @throws \LogicException
     */
    public function createDirectoryList($config)
    {
        if (!isset($config[DirectoryList::ROOT])) {
            throw new \LogicException('Magento root directory is not specified.');
        }
        $rootDir = $config[DirectoryList::ROOT][DirectoryList::PATH];
        return new DirectoryList($rootDir, $config);
    }

    /**
     * Initializes Filesystem service
     *
     * @param DirectoryList $directoryList
     * @return Filesystem
     */
    public function createFilesystem(DirectoryList $directoryList)
    {
        $driverPool = new Filesystem\DriverPool;
        return new Filesystem(
            $directoryList,
            new Filesystem\Directory\ReadFactory($driverPool),
            new Filesystem\Directory\WriteFactory($driverPool)
        );
    }
}
