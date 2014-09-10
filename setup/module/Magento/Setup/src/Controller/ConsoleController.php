<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Controller;

use Magento\Framework\DB\Adapter\Pdo\Mysql;
use Magento\Locale\Lists;
use Magento\Module\Setup\Config;
use Magento\Setup\Model\InstallerFactory;
use Magento\Setup\Model\Installer;
use Magento\Setup\Model\ConsoleLogger;
use Magento\Webapi\Exception;
use Zend\Console\Request as ConsoleRequest;
use Zend\EventManager\EventManagerInterface;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Mvc\Controller\AbstractActionController;
use \Magento\Setup\Model\UserConfigurationData as UserConfig;
use Magento\Setup\Model\AdminAccount;

/**
 * Controller that handles all setup commands via command line interface.
 *
 * @package Magento\Setup\Controller
 */
class ConsoleController extends AbstractActionController
{
    /**#@+
     * Supported command types
     */
    const CMD_INFO = 'info';
    const CMD_INSTALL = 'install';
    const CMD_INSTALL_CONFIG = 'install-configuration';
    const CMD_INSTALL_SCHEMA = 'install-schema';
    const CMD_INSTALL_DATA = 'install-data';
    const CMD_INSTALL_USER_CONFIG = 'install-user-configuration';
    const CMD_INSTALL_ADMIN_USER = 'install-admin-user';
    /**#@- */

    /**#@+
     * Additional keys for "info" command
     */
    const INFO_LOCALES = 'locales';
    const INFO_CURRENCIES = 'currencies';
    const INFO_TIMEZONES = 'timezones';
    /**#@- */

    private static $infoOptions = [
        self::CMD_INSTALL,
        self::CMD_INSTALL_CONFIG,
        self::CMD_INSTALL_SCHEMA,
        self::CMD_INSTALL_DATA,
        self::CMD_INSTALL_USER_CONFIG,
        self::CMD_INSTALL_ADMIN_USER,
        self::INFO_LOCALES,
        self::INFO_CURRENCIES,
        self::INFO_TIMEZONES,
    ];

    /**
     * @var ConsoleLogger
     */
    private $log;

    /**
     * Options Lists
     *
     * @var Lists
     */
    private $options;

    /**
     * @var Installer
     */
    private $installer;

    /**
     * CLI routes for supported command types
     *
     * @param string $type
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function getCliRoute($type)
    {
        switch ($type) {
            case self::CMD_INFO:
                $result = '(' . implode('|', self::$infoOptions) . '):type';
                break;
            case self::CMD_INSTALL:
                $result = self::getDeployConfigCliRoute() . ' ' . self::getUserConfigCliRoute()
                    . ' ' . self::getAdminUserCliRoute();
                break;
            case self::CMD_INSTALL_CONFIG:
                $result = self::getDeployConfigCliRoute();
                break;
            case self::CMD_INSTALL_SCHEMA: // break is intentionally omitted
            case self::CMD_INSTALL_DATA:
                $result = '';
                break;
            case self::CMD_INSTALL_USER_CONFIG:
                $result = self::getUserConfigCliRoute();
                break;
            case self::CMD_INSTALL_ADMIN_USER:
                $result = self::getAdminUserCliRoute();
                break;
            default:
                throw new \InvalidArgumentException("Unknown type: {$type}");
        }
        return $result ? $type . ' ' . $result : $type;
    }

    /**
     * Route for "install configuration" command
     *
     * @return string
     */
    private static function getDeployConfigCliRoute()
    {
        return '--' . Config::KEY_DB_HOST . '='
            . ' --' . Config::KEY_DB_NAME . '='
            . ' --' . Config::KEY_DB_USER . '='
            . ' --' . Config::KEY_BACKEND_FRONTNAME . '='
            . ' [--' . Config::KEY_DB_PASS . '=]'
            . ' [--' . Config::KEY_DB_PREFIX . '=]'
            . ' [--' . Config::KEY_DB_MODEL . '=]'
            . ' [--' . Config::KEY_DB_INIT_STATEMENTS . '=]'
            . ' [--' . Config::KEY_SESSION_SAVE . '=]'
            . ' [--' . Config::KEY_ENCRYPTION_KEY . '=]';
    }

    /**
     * Route for "install user configuration" command
     *
     * @return string
     */
    private static function getUserConfigCliRoute()
    {
        return '--' . UserConfig::KEY_BASE_URL . '='
            . ' --' . UserConfig::KEY_LANGUAGE . '='
            . ' --' . UserConfig::KEY_TIMEZONE . '='
            . ' --' . UserConfig::KEY_CURRENCY . '='
            . ' [--' . UserConfig::KEY_USE_SEF_URL . '=]'
            . ' [--' . UserConfig::KEY_IS_SECURE . '=]'
            . ' [--' . UserConfig::KEY_BASE_URL_SECURE . '=]'
            . ' [--' . UserConfig::KEY_IS_SECURE_ADMIN . '=]';
    }

    /**
     * Route for "install admin user" command
     *
     * @return string
     */
    private static function getAdminUserCliRoute()
    {
        return '--' . AdminAccount::KEY_USERNAME . '='
            . ' --' . AdminAccount::KEY_PASSWORD . '='
            . ' --' . AdminAccount::KEY_EMAIL . '='
            . ' --' . AdminAccount::KEY_FIRST_NAME . '='
            . ' --' . AdminAccount::KEY_LAST_NAME . '=';
    }

    /**
     * CLI Usage hints for supported command types
     *
     * @param string $type
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function getCliUsage($type)
    {
        $paramsTxt = "Available parameters:\n";
        switch ($type) {
            case self::CMD_INFO:
                return $paramsTxt . '<' . implode('|', self::$infoOptions) . '>';
            case self::CMD_INSTALL:
                return $paramsTxt
                    . self::getDeployConfigCliRoute() . "\n"
                    . self::getUserConfigCliRoute() . "\n"
                    . self::getAdminUserCliRoute();
            case self::CMD_INSTALL_CONFIG:
                return $paramsTxt . self::getDeployConfigCliRoute();
            case self::CMD_INSTALL_SCHEMA: // break is intentionally omitted
            case self::CMD_INSTALL_DATA:
                return 'This command has no parameters.';
            case self::CMD_INSTALL_USER_CONFIG:
                return $paramsTxt . self::getUserConfigCliRoute();
            case self::CMD_INSTALL_ADMIN_USER:
                return $paramsTxt . self::getAdminUserCliRoute();
            default:
                throw new \InvalidArgumentException("Unknown type: {$type}");
        }
    }

    /**
     * Constructor
     *
     * @param ConsoleLogger $consoleLogger
     * @param Lists $options
     * @param InstallerFactory $installerFactory
     */
    public function __construct(
        ConsoleLogger $consoleLogger,
        Lists $options,
        InstallerFactory $installerFactory
    ) {
        $this->log = $consoleLogger;
        $this->options = $options;
        $this->installer = $installerFactory->create($consoleLogger);
    }

    /**
     * Adding Check for Allowing only console application to come through
     *
     * {@inheritdoc}
     */
    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);
        $controller = $this;
        $events->attach('dispatch', function ($action) use ($controller) {
            /** @var $action \Zend\Mvc\Controller\AbstractActionController */
            // Make sure that we are running in a console and the user has not tricked our
            // application into running this action from a public web server.
            if (!$action->getRequest() instanceof ConsoleRequest) {
                throw new \RuntimeException('You can only use this action from a console!');
            }
        }, 100); // execute before executing action logic
        return $this;
    }

    /**
     * Controller for Install Command
     *
     * @return void
     * @throws \Exception
     */
    public function installAction()
    {
        try {
            /** @var \Zend\Console\Request $request */
            $request = $this->getRequest();
            $params = $request->getParams();
            $this->installer->install($params, $params, $params);
        } catch (Exception $e) {
            $this->log->logError($e);
        }
    }

    /**
     * Creates the local.xml file
     *
     * @return void
     * @throws \Exception
     */
    public function installDeploymentConfigAction()
    {
        /** @var \Zend\Console\Request $request */
        $request = $this->getRequest();
        $this->installer->installDeploymentConfig($request->getParams());
    }

    /**
     * Installs and updates database schema
     *
     * @return void
     * @throws \Exception
     */
    public function installSchemaAction()
    {
        $this->installer->installSchema($this->log);
    }

    /**
     * Installs and updates data fixtures
     *
     * @return void
     * @throws \Exception
     */
    public function installDataAction()
    {
        $this->installer->installDataFixtures($this->log);
    }

    /**
     * Installs user configuration
     */
    public function installUserConfigAction()
    {
        /** @var \Zend\Console\Request $request */
        $request = $this->getRequest();
        $this->installer->installUserConfig($request->getParams());
    }

    /**
     * Installs admin user
     */
    public function installAdminUserAction()
    {
        /** @var \Zend\Console\Request $request */
        $request = $this->getRequest();
        $this->installer->installAdminUser($request->getParams());
    }

    /**
     * Shows necessary information for installing Magento
     *
     * @return string
     * @throws \Exception
     */
    public function infoAction()
    {
        $type = $this->getRequest()->getParam('type');
        switch($type) {
            case self::CMD_INSTALL:
            case self::CMD_INSTALL_CONFIG:
            case self::CMD_INSTALL_SCHEMA:
            case self::CMD_INSTALL_DATA:
            case self::CMD_INSTALL_USER_CONFIG:
            case self::CMD_INSTALL_ADMIN_USER:
                return self::getCliUsage($type);
            case self::INFO_LOCALES:
                return $this->arrayToString($this->options->getLocaleList());
            case self::INFO_CURRENCIES:
                return $this->arrayToString($this->options->getCurrencyList());
            case self::INFO_TIMEZONES:
                return $this->arrayToString($this->options->getTimezoneList());
            default:
                throw new \InvalidArgumentException("Unknown type: {$type}");
        }
    }

    /**
     * Convert an array to string
     *
     * @param array $input
     * @return string
     */
    private function arrayToString($input)
    {
        $result = '';
        foreach ($input as $key => $value) {
            $result .= "$key => $value\n";
        }
        return $result;
    }
}
