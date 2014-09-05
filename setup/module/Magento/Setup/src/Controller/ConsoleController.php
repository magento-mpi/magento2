<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Controller;

use Magento\Config\ConfigFactory;
use Magento\Module\ModuleListInterface;
use Magento\Framework\DB\Adapter\Pdo\Mysql;
use Magento\Framework\Math\Random;
use Magento\Module\SetupFactory;
use Magento\Locale\Lists;
use Magento\Module\Setup\Config;
use Magento\Setup\Helper\Helper;
use Magento\Setup\Model\DatabaseCheck;
use Magento\Setup\Model\FilePermissions;
use Zend\Mvc\Controller\AbstractActionController;
use Magento\Setup\Model\AdminAccountFactory;
use Zend\Console\Request as ConsoleRequest;
use Zend\Console\Console;
use Zend\EventManager\EventManagerInterface;
use Zend\Stdlib\RequestInterface as Request;

/**
 * Class ConsoleController
 * Controller that handles all setup commands via command line interface.
 *
 * @package Magento\Setup\Controller
 */
class ConsoleController extends AbstractActionController
{
    /**
     * File Permissions
     *
     * @var FilePermissions
     */
    protected $filePermission;

    /**
     * Options Lists
     *
     * @var Lists
     */
    protected $list;

    /**
     * Module Lists
     *
     * @var array
     */
    protected $moduleList;

    /**
     * Configurations
     *
     * @var Config
     */
    protected $config;

    /**
     * Factory Configurations
     *
     * @var Config
     */
    protected $factoryConfig;

    /**
     * Random Generator
     *
     * @var Random
     */
    protected $random;

    /**
     * Admin Account Factory
     *
     * @var AdminAccountFactory
     */
    protected $adminAccountFactory;

    /**
     * Console
     *
     * @var \Zend\Console\Console
     */
    protected $console;

    /**
     * Default Constructor
     *
     * @param FilePermissions $filePermission
     * @param Lists $list
     * @param ConfigFactory $configFactory
     * @param Random $random
     * @param Config $config
     * @param ModuleListInterface $moduleList
     * @param SetupFactory $setupFactory
     * @param AdminAccountFactory $adminAccountFactory
     */
    public function __construct(
        FilePermissions $filePermission,
        Lists $list,
        ConfigFactory $configFactory,
        Random $random,
        Config $config,
        ModuleListInterface $moduleList,
        SetupFactory $setupFactory,
        AdminAccountFactory $adminAccountFactory
    ) {
        $this->filePermission = $filePermission;
        $this->list = $list;
        $this->factoryConfig = $configFactory->create();
        $this->random = $random;
        $this->config = $config;
        $this->setupFactory = $setupFactory;
        $this->moduleList = $moduleList->getModules();
        $this->adminAccountFactory = $adminAccountFactory;
        $this->console = Console::getInstance();
    }

    /**
     * Adding Check for Allowing only console application to come through
     *
     * @param  EventManagerInterface $events
     * @return AbstractController
     */
    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);
        $controller = $this;
        $events->attach('dispatch', function ($e) use ($controller) {
            /** @var $e \Zend\Mvc\Controller\AbstractActionController */
            // Make sure that we are running in a console and the user has not tricked our
            // application into running this action from a public web server.
            if (!$e->getRequest() instanceof ConsoleRequest) {
                throw new \RuntimeException('You can only use this action from a console!');
            }
        }, 100); // execute before executing action logic
        return $this;
    }

    /**
     * Controller for Install Command
     * @return string
     * @throws \Exception
     */
    public function installAction()
    {
        $message = [];
        $message[] = $this->installDeploymentConfigAction();
        $message[] = $this->installSchemaAction();
        $message[] = $this->installDataAction();
        return implode(PHP_EOL, $message);
    }

    /**
     * Creates the local.xml file
     *
     * @throws \Exception
     */
    public function installDeploymentConfigAction()
    {

        $this->console->writeLine("Starting to install Deployment Configuration Data.");
        $request = $this->getRequest();

        //Checking license agreement
        $license   = $request->getParam('license_agreement_accepted');
        if ($license !== 'yes') {
            throw new \Exception('You have to agree on license requirements to proceed.');
        }

        //Setting the basePath of Magento application
        $magentoDir = $request->getParam('magentoDir');
        $this->updateMagentoDirectory($magentoDir);

        //Check File permission
        Helper::checkAndCreateDirectory($this->factoryConfig->getMagentoBasePath() . '/var/cache');
        Helper::checkAndCreateDirectory($this->factoryConfig->getMagentoBasePath() . '/var/log');
        Helper::checkAndCreateDirectory($this->factoryConfig->getMagentoBasePath() . '/var/session');
        $required = $this->filePermission->getRequired();
        $current = $this->filePermission->getCurrent();
        if (array_diff($required, $current)) {
            throw new \Exception('You do no have appropriate file permissions.');
        }

        //Db Data Configuration
        $dbHost   = $request->getParam('db_host', '');
        $dbName   = $request->getParam('db_name', '');
        $dbUser   = $request->getParam('db_user', '');
        $dbPass   = $request->getParam('db_pass', '');
        $dbPrefix = $request->getParam('db_table_prefix', '');
        $adminUrl = $request->getParam('admin_url');

        //Checks Database Connectivity
        $this->checkDatabaseConnection($dbName, $dbHost, $dbUser, $dbPass);

        $data = array(
            'db' => array(
                'useExistingDB' => 1,
                'useAccess' => 1,
                'user' => $dbUser,
                'password' => $dbPass,
                'host' => $dbHost,
                'name' => $dbName,
                'tablePrefix' => $dbPrefix,
            ),
            'config' => array(
                'address' => array(
                    'admin' => $adminUrl,
                )
            ),
        );

        $this->config->setConfigData($this->config->convertFromDataObject($data));
        //Creates Deployment Configuration
        $this->config->install();
        $this->console->writeLine("Completed: Deployment Configuration.");
    }

    /**
     * Installs and updates database schema
     *
     * @throws \Exception
     */
    public function installSchemaAction()
    {
        $this->console->writeLine("Starting to install Schema");
        $request = $this->getRequest();

        //Setting the basePath of Magento application
        $magentoDir = $request->getParam('magentoDir');
        $this->updateMagentoDirectory($magentoDir);

        $this->config->setConfigData($this->config->getConfigurationFromDeploymentFile());
        $this->setupFactory->setConfig($this->config->getConfigData());

        //List of All Module Names
        $moduleNames = array_keys($this->moduleList);

        // Do schema updates for each module
        foreach ($moduleNames as $moduleName) {
            $setup = $this->setupFactory->create($moduleName);
            $setup->applyUpdates();
            $this->console->writeLine("Installed schema : " . $moduleName);
        }

        $this->console->writeLine("Installing recurring post-schema updates.");
        // Do post-schema updates for each module
        foreach ($moduleNames as $moduleName) {
            $setup = $this->setupFactory->create($moduleName);
            $setup->applyRecurringUpdates();
        }

        $this->console->writeLine("Completed: Schema Installation");
    }

    /**
     * Installs and updates data
     *
     * @throws \Exception
     */
    public function installDataAction()
    {
        $this->console->writeLine("Starting to install Data Updates");
        $request = $this->getRequest();

        //Setting the basePath of Magento application
        $magentoDir = $request->getParam('magentoDir');
        $this->updateMagentoDirectory($magentoDir);

        //Data Information
        $storeUrl       = $request->getParam('store_url');
        $secureStoreUrl = $request->getParam('secure_store_url', false) == 'yes' ? true : false;
        $secureAdminUrl = $request->getParam('secure_admin_url', false) == 'yes' ? true : false;
        $useRewrites    = $request->getParam('use_rewrites', false) == 'yes' ? true : false;
        $encryptionKey = $request->getParam('encryption_key', $this->getRandomEncryptionKey());
        $locale   = $request->getParam('locale');
        $timezone   = $request->getParam('timezone');
        $currency   = $request->getParam('currency');
        $adminLstname   = $request->getParam('admin_lastname');
        $adminFirstname   = $request->getParam('admin_firstname');
        $adminEmail   = $request->getParam('admin_email');
        $adminUsername   = $request->getParam('admin_username');
        $adminPassword   = $request->getParam('admin_password');

        $data = array(
            'admin' => array(
                'password' => $adminPassword,
                'username' => $adminUsername,
                'email' => $adminEmail,
                'confirm' => $adminPassword,
                'lastname' => $adminLstname,
                'firstname' => $adminFirstname,
            ),
            'store' => array(
                'timezone' => $timezone,
                'currency' => $currency,
                'language' => $locale,
                'usaSampleData' => false,
            ),
            'config' => array(
                'address' => array(
                    'front' => $storeUrl,
                ),
                'https' => array(
                    'web' => $secureStoreUrl,
                    'admin' => $secureAdminUrl,
                ),
                'rewrites' => array(
                    'allowed' => $useRewrites,
                ),
                'encrypt' => array(
                    'type' => 'magento',
                    'key' => $encryptionKey,
                ),
                'advanced' => array(
                    'expanded' => true,
                ),
            ),
        );

        $this->config->setConfigData($this->config->convertFromDataObject($data));
        $this->config->addConfigData($this->config->getConfigurationFromDeploymentFile());
        $this->setupFactory->setConfig($this->config->getConfigData());

        $setup = $this->setupFactory->create('');

        $setup->addConfigData(
            'web/seo/use_rewrites',
            isset($data['config']['rewrites']['allowed']) ? $data['config']['rewrites']['allowed'] : 0
        );

        $setup->addConfigData(
            'web/unsecure/base_url',
            isset($data['config']['address']['front']) ? $data['config']['address']['front'] : '{{unsecure_base_url}}'
        );
        $setup->addConfigData(
            'web/secure/use_in_frontend',
            isset($data['config']['https']['web']) ? $data['config']['https']['web'] : 0
        );
        $setup->addConfigData(
            'web/secure/base_url',
            isset($data['config']['address']['front']) ? $data['config']['address']['front'] : '{{secure_base_url}}'
        );
        $setup->addConfigData(
            'web/secure/use_in_adminhtml',
            isset($data['config']['https']['admin']) ? $data['config']['https']['admin'] : 0
        );
        $setup->addConfigData(
            'general/locale/code',
            isset($data['store']['language']) ? $data['store']['language'] : 'en_US'
        );
        $setup->addConfigData(
            'general/locale/timezone',
            isset($data['store']['timezone']) ? $data['store']['timezone'] : 'America/Los_Angeles'
        );

        $currencyCode = isset($data['store']['currency']) ? $data['store']['currency'] : 'USD';

        $setup->addConfigData('currency/options/base', $currencyCode);
        $setup->addConfigData('currency/options/default', $currencyCode);
        $setup->addConfigData('currency/options/allow', $currencyCode);

        // Create administrator account
        $this->adminAccountFactory->setConfig($this->config->getConfigData());
        $adminAccount = $this->adminAccountFactory->create($setup);
        $adminAccount->save();

        if ($data['config']['encrypt']['type'] == 'magento') {
            $key = $this->getRandomEncryptionKey();
        } else {
            $key = $data['config']['encrypt']['key'];
        }

        $this->config->replaceTmpEncryptKey($key);
        $this->config->replaceTmpInstallDate(date('r'));

        $phpPath = Helper::phpExecutablePath();
        exec(
            $phpPath .
            'php -f ' . $this->factoryConfig->getMagentoBasePath(). '/dev/shell/run_data_fixtures.php',
            $output,
            $exitCode
        );
        if ($exitCode !== 0) {
            $outputMsg = implode(PHP_EOL, $output);
            throw new \Exception('Data Update Failed with Exit Code: ' . $exitCode . PHP_EOL . $outputMsg);
        }
        $this->console->writeLine("Completed: Data Installation.");
    }


    /**
     * Shows necessary information for installing Magento
     *
     * @return string
     * @throws \Exception
     */
    public function infoAction()
    {
        $request = $this->getRequest();

        switch($request->getParam('type')){
            case 'locales':
                return  Helper::arrayToString($this->list->getLocaleList());
                break;
            case 'currencies':
                return  Helper::arrayToString($this->list->getCurrencyList());
                break;
            case 'timezones':
                return  Helper::arrayToString($this->list->getTimezoneList());
                break;
            case 'options':
            default:
                return  Helper::showInstallationOptions();
                break;
        }
    }

    /**
     * Updates Magento Directory
     *
     * @param string $magentoDir
     * @return void
     */
    private function updateMagentoDirectory($magentoDir)
    {
        if ($magentoDir) {
            $this->factoryConfig->setMagentoBasePath(rtrim(str_replace('\\', '/', realpath($magentoDir))), '/');
        } else {
            $this->factoryConfig->setMagentoBasePath();
        }
    }

    /**
     * Creates Random Encryption Key
     *
     * @return string
     */
    private function getRandomEncryptionKey()
    {
        return md5($this->random->getRandomString(10));
    }

    /**
     * Checks Database Connection
     *
     * @param string $dbName
     * @param string $dbHost
     * @param string $dbUser
     * @param string $dbPass
     * @return void
     * @throws \Exception
     */
    private function checkDatabaseConnection($dbName, $dbHost, $dbUser, $dbPass)
    {
        //Check DB connection
        $dbConnectionInfo = array(
            'driver' => "Pdo",
            'dsn' => "mysql:dbname=" . $dbName . ";host=" . $dbHost,
            'username' => $dbUser,
            'password' => $dbPass,
            'driver_options' => array(
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"
            ),
        );
        $checkDB = new DatabaseCheck($dbConnectionInfo);
        if (!$checkDB->checkConnection()) {
            throw new \Exception('Database connection failure.');
        }
    }


}
