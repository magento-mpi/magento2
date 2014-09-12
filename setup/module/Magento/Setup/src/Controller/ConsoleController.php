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
use Magento\Setup\Framework\DB\Adapter\Pdo\Mysql;
use Magento\Module\SetupFactory;
use Magento\Locale\Lists;
use Magento\Module\Setup\Config;
use Magento\Setup\Helper\Helper;
use Magento\Setup\Model\ConsoleLogger;
use Magento\Setup\Model\FilePermissions;
use Magento\Setup\Model\UserConfigurationDataFactory;
use Magento\Setup\Model\AdminAccountFactory;
use Zend\Console\Request as ConsoleRequest;
use Zend\EventManager\EventManagerInterface;
use Zend\Stdlib\RequestInterface as Request;
use Symfony\Component\Process\PhpExecutableFinder;
use Zend\Mvc\Controller\AbstractController;

/**
 * Class ConsoleController
 * Controller that handles all setup commands via command line interface.
 *
 * @package Magento\Setup\Controller
 */
class ConsoleController extends AbstractInstallActionController
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
    protected $options;

    /**
     * Default Constructor
     *
     * @param FilePermissions $filePermission
     * @param Lists $options
     * @param ModuleListInterface $moduleList
     * @param SetupFactory $setupFactory
     * @param AdminAccountFactory $adminAccountFactory
     * @param Config $config
     * @param ConfigFactory $systemConfig
     * @param UserConfigurationDataFactory $userConfigurationDataFactory
     * @param ConsoleLogger $consoleLogger
     * @param PhpExecutableFinder $phpExecutableFinder
     */
    public function __construct(
        FilePermissions $filePermission,
        Lists $options,
        ModuleListInterface $moduleList,
        SetupFactory $setupFactory,
        AdminAccountFactory $adminAccountFactory,
        Config $config,
        ConfigFactory $systemConfig,
        UserConfigurationDataFactory $userConfigurationDataFactory,
        ConsoleLogger $consoleLogger,
        PhpExecutableFinder $phpExecutableFinder
    ) {
        parent::__construct($moduleList,
            $setupFactory,
            $adminAccountFactory,
            $config,
            $systemConfig,
            $userConfigurationDataFactory,
            $consoleLogger,
            $phpExecutableFinder
        );
        $this->filePermission = $filePermission;
        $this->options = $options;
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
        $this->logger->log("Beginning Magento Installation...");
        $this->installDeploymentConfigAction();
        $this->installSchemaAction();
        $this->installDataAction();
        $this->logger->log("Completed Magento Installation Successfully.");
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

        //Checking license agreement
        $license   = $request->getParam('license_agreement_accepted');
        if ($license !== 'yes') {
            throw new \Exception('You have to agree on license requirements to proceed.');
        }

        //Setting the basePath of Magento application
        $magentoDir = $request->getParam('magentoDir');
        $this->setMagentoBasePath($magentoDir);

        //Check File permission
        $results = $this->filePermission->checkPermission();
        if ($results) {
            $errorMsg = 'You do no have appropriate file permissions for the following: ';
            foreach ($results as $result) {
                $errorMsg .= '\'' . $result . '\' ';
            }
            throw new \Exception($errorMsg);
        }

        //Db Data Configuration
        $dbHost   = $request->getParam('db_host', '');
        $dbName   = $request->getParam('db_name', '');
        $dbUser   = $request->getParam('db_user', '');
        $dbPass   = $request->getParam('db_pass', '');
        $dbPrefix = $request->getParam('db_table_prefix', '');
        $adminUrl = $request->getParam('admin_url');
        $encryptionKey = $request->getParam('encryption_key', null);

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
                ),
                'encrypt' => array(
                    'key' => $encryptionKey,
                ),
            )
        );

        $this->installDeploymentConfiguration($data);
    }

    /**
     * Installs and updates database schema
     *
     * @return void
     * @throws \Exception
     */
    public function installSchemaAction()
    {
        //Setting the basePath of Magento application
        $magentoDir = $this->getRequest()->getParam('magentoDir');
        $this->setMagentoBasePath($magentoDir);

        $this->config->setConfigData($this->config->getConfigurationFromDeploymentFile());
        $this->installSchemaUpdates();
    }

    /**
     * Installs and updates data
     *
     * @return void
     * @throws \Exception
     */
    public function installDataAction()
    {
        /** @var \Zend\Console\Request $request */
        $request = $this->getRequest();

        //Setting the basePath of Magento application
        $magentoDir = $request->getParam('magentoDir');
        $this->setMagentoBasePath($magentoDir);

        $this->installDataUpdates();
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
                return  $this->arrayToString($this->options->getLocaleList());
                break;
            case 'currencies':
                return  $this->arrayToString($this->options->getCurrencyList());
                break;
            case 'timezones':
                return  $this->arrayToString($this->options->getTimezoneList());
                break;
            case 'options':
            default:
                return  $this->showInstallationOptions();
                break;
        }
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

    /**
     * Updates Magento Directory
     *
     * @param string $magentoDir
     * @return void
     */
    protected function setMagentoBasePath($magentoDir)
    {
        if ($magentoDir) {
            $this->systemConfig->setMagentoBasePath(rtrim(str_replace('\\', '/', realpath($magentoDir))), '/');
        } else {
            $this->systemConfig->setMagentoBasePath();
        }
    }

}
