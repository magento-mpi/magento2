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
use Magento\Setup\Model\FilePermissions;
use Magento\Setup\Model\UserConfigurationDataFactory;
use Zend\Mvc\Controller\AbstractActionController;
use Magento\Setup\Model\AdminAccountFactory;
use Zend\Console\Request as ConsoleRequest;
use Zend\Console\Console;
use Zend\EventManager\EventManagerInterface;
use Zend\Stdlib\RequestInterface as Request;
use Symfony\Component\Process\PhpExecutableFinder;

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
     * @var ModuleListInterface
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
     * User Configuration Data Factory
     *
     * @var UserConfigurationDataFactory
     */
    protected $userConfigurationDataFactory;

    /**
     * Console
     *
     * @var \Zend\Console\Console
     */
    protected $console;

    /**
     * PHP executable finder
     *
     * @var \Symfony\Component\Process\PhpExecutableFinder
     */
     protected $phpExecutableFinder;


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
     * @param UserConfigurationDataFactory $userConfigurationDataFactory
     * @param PhpExecutableFinder $phpExecutableFinder
     */
    public function __construct(
        FilePermissions $filePermission,
        Lists $list,
        ConfigFactory $configFactory,
        Random $random,
        Config $config,
        ModuleListInterface $moduleList,
        SetupFactory $setupFactory,
        AdminAccountFactory $adminAccountFactory,
        UserConfigurationDataFactory $userConfigurationDataFactory,
        PhpExecutableFinder $phpExecutableFinder
    ) {
        $this->filePermission = $filePermission;
        $this->list = $list;
        $this->factoryConfig = $configFactory->create();
        $this->random = $random;
        $this->config = $config;
        $this->setupFactory = $setupFactory;
        $this->moduleList = $moduleList;
        $this->adminAccountFactory = $adminAccountFactory;
        $this->userConfigurationDataFactory = $userConfigurationDataFactory;
        $this->console = Console::getInstance();
        $this->logger = new \Logger(Console::getInstance());
        $this->phpExecutableFinder = $phpExecutableFinder;
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
        $events->attach('dispatch', function ($action) use ($controller) {
            /** @var $e \Zend\Mvc\Controller\AbstractActionController */
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
     * @throws \Exception
     */
    public function installAction()
    {
        $this->console->writeLine("Beginning Magento Installation...");
        $this->installDeploymentConfigAction();
        $this->installSchemaAction();
        $this->installDataAction();
        $this->console->writeLine("Completed Magento Installation Successfully.");
    }

    /**
     * Creates the local.xml file
     *
     * @throws \Exception
     */
    public function installDeploymentConfigAction()
    {
        $this->console->writeLine("Starting to install Deployment Configuration Data.");

        /** @var \Zend\Console\Request $request */
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
        if (!$this->filePermission->checkPermission()) {
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
        Helper::checkDatabaseConnection($dbName, $dbHost, $dbUser, $dbPass);

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
            'config' => array(
                'address' => array(
                    'admin' => $adminUrl,
                ),
            ),
        );

        $this->config->setConfigData($this->config->convertFromDataObject($data));
        //Creates Deployment Configuration
        $this->config->install();

        //Finalizes the deployment configuration
        $encryptionKey = $request->getParam('encryption_key');
        if (!$encryptionKey) {
            $key = $this->getRandomEncryptionKey();
        } else {
            $key = $encryptionKey;
        }

        $this->config->replaceTmpEncryptKey($key);
        $this->config->replaceTmpInstallDate(date('r'));

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

        //Setting the basePath of Magento application
        $magentoDir = $this->getRequest()->getParam('magentoDir');
        $this->updateMagentoDirectory($magentoDir);

        $this->config->setConfigData($this->config->getConfigurationFromDeploymentFile());
        $this->setupFactory->setConfig($this->config->getConfigData());

        //List of All Module Names
        $moduleNames = array_keys($this->moduleList->getModules());

        // Do schema updates for each module
        foreach ($moduleNames as $moduleName) {
            $setup = $this->setupFactory->create($moduleName);
            $setup->applyUpdates();
            $this->console->writeLine("Installed schema : " . $moduleName);
        }

        $this->console->writeLine("Installing recurring post-schema updates for all modules.");
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
        /** @var \Zend\Console\Request $request */
        $request = $this->getRequest();

        //Setting the basePath of Magento application
        $magentoDir = $request->getParam('magentoDir');
        $this->updateMagentoDirectory($magentoDir);

        $data = $this->parseUserConfigurationData($request);
        $this->installUserConfigurationData($data);

        $this->installDataUpdates();
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
        switch($this->getRequest()->getParam('type')){
            case 'locales':
                return  $this->arrayToString($this->list->getLocaleList());
                break;
            case 'currencies':
                return  $this->arrayToString($this->list->getCurrencyList());
                break;
            case 'timezones':
                return  $this->arrayToString($this->list->getTimezoneList());
                break;
            case 'options':
            default:
                return  $this->showInstallationOptions();
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
     * Installs User Configuration Data
     *
     * @param array $data
     * @return void
     */
    protected function installUserConfigurationData($data)
    {
        $setup = $this->setupFactory->create();

        //Loading Configurations
        $this->config->setConfigData($this->config->convertFromDataObject($data));
        $this->config->addConfigData($this->config->getConfigurationFromDeploymentFile());
        $this->userConfigurationDataFactory->setConfig($this->config->getConfigData());
        $this->userConfigurationDataFactory->create($setup)->install($data);

        // Create administrator account
        $this->adminAccountFactory->setConfig($this->config->getConfigData());
        $adminAccount = $this->adminAccountFactory->create($setup);
        $adminAccount->save();
    }

    /**
     * Parses User Configuration Data from Request Parameters
     *
     * @param \Zend\Console\Request $request
     * @return array
     */
    protected function parseUserConfigurationData($request)
    {
        $storeUrl = $request->getParam('store_url');
        $secureStoreUrl = $request->getParam('secure_store_url', false) == 'yes' ? true : false;
        $secureAdminUrl = $request->getParam('secure_admin_url', false) == 'yes' ? true : false;
        $useRewrites = $request->getParam('use_rewrites', false) == 'yes' ? true : false;
        $locale = $request->getParam('locale');
        $timezone = $request->getParam('timezone');
        $currency = $request->getParam('currency');
        $adminLstname = $request->getParam('admin_lastname');
        $adminFirstname = $request->getParam('admin_firstname');
        $adminEmail = $request->getParam('admin_email');
        $adminUsername = $request->getParam('admin_username');
        $adminPassword = $request->getParam('admin_password');

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
                'advanced' => array(
                    'expanded' => true,
                ),
            ),
        );
        return $data;
    }

    /**
     * Installs Data Updates
     *
     * @return int
     * @throws \Exception
     */
    protected function installDataUpdates()
    {
        $exitCode = null;
        $output = null;
        $phpPath = $this->phpExecutableFinder->find();

        exec(
            $phpPath .
            '/php -f ' . $this->factoryConfig->getMagentoBasePath() . '/dev/shell/run_data_fixtures.php',
            $output,
            $exitCode
        );
        if ($exitCode !== 0) {
            $outputMsg = implode(PHP_EOL, $output);
            throw new \Exception('Data Update Failed with Exit Code: ' . $exitCode . PHP_EOL . $outputMsg);
        }
        return $exitCode;
    }

    /**
     * Convert an array to string
     *
     * @param array $input
     * @return string
     */
    protected function arrayToString($input)
    {
        $result = '';
        foreach ($input as $key => $value) {
            $result .= "$key => $value\n";
        }

        return $result;
    }

    /**
     * Show installation options
     *
     * @return string
     */
    protected function showInstallationOptions()
    {
        return "\n" . 'Required parameters:' ."\n\n" .
        'license_agreement_accepted => Accept licence. See LICENSE*.txt. Flag value.' ."\n"
        . 'locale => Locale to use. Run with "show locales" for full list' ."\n"
        . 'timezone => Time zone to use. Run with "show timezones" for full list' ."\n"
        . 'currency => Default currency. Run with "show currencies" for full list' ."\n"
        . 'db_host => IP or name of your DB host' ."\n"
        . 'db_name => Database name' ."\n"
        . 'db_user => Database user name' ."\n"
        . 'store_url => Store URL. For example, "http://myinstance.com"' ."\n"
        . 'admin_url => Admin Front Name. For example, "admin"' ."\n"
        . 'admin_lastname => Admin user last name' ."\n"
        . 'admin_firstname => Admin user first name' ."\n"
        . 'admin_email => Admin email' ."\n"
        . 'admin_username => Admin login' ."\n"
        . 'admin_password => Admin password' ."\n\n"
        . 'Optional parameters:' ."\n\n"
        . 'magentoDir => The Magento application directory.' ."\n"
        . 'use_rewrites => Use web server rewrites. Value "yes"/"no".' ."\n"
        . 'db_pass => DB password. Empty by default' ."\n"
        . 'db_table_prefix => Use prefix for tables of this installation. Empty by default' ."\n"
        . 'secure_store_url => Full secure URL for store. For example "https://myinstance.com"' ."\n"
        . 'secure_admin_url => Full secure URL for admin . For example "https://myinstance.com/admin"' ."\n"
        . 'encryption_key => Key to encrypt sensitive data. Auto-generated if not provided' ."\n";
    }

}
