<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\App;

use Magento\Framework\Profiler;
use \Magento\Framework\AppInterface;

/**
 * A bootstrap of Magento application
 *
 * Performs basic initialization root function: injects init parameters and creates object manager
 * Can create/run applications
 */
class Bootstrap
{
    /**#+
     * Possible errors that can be triggered by the bootstrap
     */
    const ERR_MAINTENANCE = 901;
    const ERR_IS_INSTALLED = 902;
    /**#- */

    /**
     * The initialization parameters (normally come from the $_SERVER)
     *
     * @var array
     */
    private $server;

    /**
     * Whether maintenance mode is required or not
     *
     * True: maintenance mode is required to be "On"
     * False: maintenance mode is required to be "Off"
     *
     * @var bool
     */
    private $isMaintenanceRequired = false;

    /**
     * Whether application must be installed or not
     *
     * True: application must be already installed
     * False: application must not be installed yet
     *
     * @var bool
     */
    private $isInstalledRequired = true;

    /**
     * Root directory
     *
     * @var string
     */
    private $rootDir;

    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManager
     */
    private $objectManager;

    /**
     * Directory list
     *
     * @var Filesystem\DirectoryList
     */
    private $dirList;

    /**
     * Maintenance mode manager
     *
     * @var \Magento\Framework\App\MaintenanceMode
     */
    private $maintenance;

    /**
     * Bootstrap-specific error code that may have been set in runtime
     *
     * @var bool|int
     */
    private $errorCode = false;

    /**
     * Constructor
     *
     * @param string $rootDir
     * @param array $params
     */
    public function __construct($rootDir, array $params)
    {
        $this->rootDir = $rootDir;
        $this->server = $params;
    }

    /**
     * Injects additional initialization parameters
     *
     * @param array $params
     */
    public function addParams(array $params)
    {
        $this->server = array_replace_recursive($this->server, $params);
        $this->reset();
    }

    /**
     * Gets the current parameters
     *
     * @return array
     */
    public function getParams()
    {
        return $this->server;
    }

    /**
     * Sets whether maintenance is required to run the application
     *
     * @param bool $value
     */
    public function setIsMaintenanceRequirement($value)
    {
        $this->isMaintenanceRequired = $value;
        $this->reset();
    }

    /**
     * Sets whether "installed" is required to run the application
     *
     * @param bool $value
     */
    public function setIsInstalledRequirement($value)
    {
        $this->isInstalledRequired = $value;
        $this->reset();
    }

    /**
     * Factory method for creating application instances
     *
     * @param string $type
     * @param array $arguments
     * @return \Magento\Framework\AppInterface
     * @throws \InvalidArgumentException
     */
    public function createApplication($type, $arguments = [])
    {
        try {
            $this->init();
            $application = $this->objectManager->create($type, $arguments);
            if (!($application instanceof AppInterface)) {
                throw new \InvalidArgumentException("The provided class doesn't implement AppInterface: {$type}");
            }
            return $application;
        } catch (\Exception $e) {
            $this->terminate($e);
        }
    }

    /**
     * Runs an application
     *
     * @param \Magento\Framework\AppInterface $application
     */
    public function run(AppInterface $application)
    {
        try {
            try {
                \Magento\Framework\Profiler::start('magento');
                $this->init();
                $this->assertMaintenance($this->isMaintenanceRequired);
                $this->assertInstalled($this->isInstalledRequired);
                $response = $application->launch();
                $response->sendResponse();
                \Magento\Framework\Profiler::stop('magento');
            } catch (\Exception $e) {
                \Magento\Framework\Profiler::stop('magento');
                if (!$application->catchException($this, $e)) {
                    throw $e;
                }
            }
        } catch (\Exception $e) {
            $this->terminate($e);
        }
    }

    /**
     * Asserts maintenance mode
     *
     * @param bool $isExpected
     * @throws \Exception
     */
    private function assertMaintenance($isExpected)
    {
        $this->init();
        $isOn = $this->maintenance->isOn(isset($this->server['REMOTE_ADDR']) ? $this->server['REMOTE_ADDR'] : '');
        if ($isOn && !$isExpected) {
            $this->errorCode = self::ERR_MAINTENANCE;
            throw new \Exception('Unable to proceed: the maintenance mode is enabled.');
        }
        if (!$isOn && $isExpected) {
            $this->errorCode = self::ERR_MAINTENANCE;
            throw new \Exception('Unable to proceed: the maintenance mode must be enabled first.');
        }
    }

    /**
     * Asserts whether application is installed
     *
     * @param bool $isExpected
     * @throws \Exception
     */
    private function assertInstalled($isExpected)
    {
        $this->init();
        $isInstalled = $this->isInstalled();
        if (!$isInstalled && $isExpected) {
            $this->errorCode = self::ERR_IS_INSTALLED;
            throw new \Exception('Application is not installed yet.');
        }
        if ($isInstalled && !$isExpected) {
            $this->errorCode = self::ERR_IS_INSTALLED;
            throw new \Exception('Application is already installed.');
        }
    }

    /**
     * Determines whether application is installed
     *
     * @return bool
     */
    private function isInstalled()
    {
        $this->init();
        return file_exists($this->dirList->getDir(Filesystem::CONFIG_DIR) . '/local.xml');
    }

    /**
     * Gets the object manager instance
     *
     * @return \Magento\Framework\ObjectManager
     */
    public function getObjectManager()
    {
        $this->init();
        return $this->objectManager;
    }

    /**
     * Resets any initialized objects (if there was initialization)
     *
     * @return void
     */
    private function reset()
    {
        $this->errorCode = false;
        $this->objectManager = null;
        $this->dirList = null;
        $this->maintenance = null;
    }

    /**
     * Initializes the essential objects
     *
     * @return void
     */
    private function init()
    {
        if (!$this->objectManager) {
            $factory = new ObjectManagerFactory;
            $this->objectManager = $factory->create($this->rootDir, $this->server);
            $this->dirList = $this->objectManager->get('Magento\Framework\App\Filesystem\DirectoryList');
            $this->maintenance = $this->objectManager->get('Magento\Framework\App\MaintenanceMode');
        }
    }

    /**
     * Getter for error code
     *
     * @return bool|int
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * Checks whether developer mode is set in the initialization parameters
     *
     * @return bool
     */
    public function isDeveloperMode()
    {
        return isset($this->server[State::PARAM_MODE]) && $this->server[State::PARAM_MODE] == State::MODE_DEVELOPER;
    }

    /**
     * Display an exception and terminate program execution
     *
     * @param \Exception $e
     */
    public function terminate(\Exception $e)
    {
        if ($this->isDeveloperMode()) {
            echo $e;
        } else {
            $message = "An error has happened during application run. See debug log for details.\n";
            try {
                if (!$this->objectManager) {
                    throw new \DomainException();
                }
                $this->objectManager->get('Magento\Framework\Logger')->logException($e);
            } catch (\Exception $e) {
                $message .= "Could not write error message to log. Please use developer mode to see the message.\n";
            }
            echo $message;
        }
        exit(1);
    }
}
