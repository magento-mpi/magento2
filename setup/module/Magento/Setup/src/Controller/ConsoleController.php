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

class ConsoleController extends AbstractActionController
{
    /**
     * @var FilePermissions
     */
    protected $filePermission;

    /**
     * @var Lists
     */
    protected $list;

    /**
     * @var []
     */
    protected $moduleList;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Config
     */
    protected $factoryConfig;

    /**
     * @var Random
     */
    protected $random;

    /**
     * @var AdminAccountFactory
     */
    protected $adminAccountFactory;

    /**
     * @param \Magento\Setup\Model\FilePermissions $filePermission
     * @param \Magento\Locale\Lists $list
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
        $this->configFactory = $configFactory;
        $this->factoryConfig = $this->configFactory->create();
        $this->random = $random;
        $this->config = $config;
        $this->setupFactory = $setupFactory;
        $this->moduleList = $moduleList->getModules();
        $this->adminAccountFactory = $adminAccountFactory;
    }

    /**
     * Creates the local.xml file
     * @return string
     * @throws \Exception
     */
    public function installLocalAction()
    {
        $request = $this->getRequest();

        //Validating the request
        Helper::checkRequest($request);

        //Checking license agreement
        $license   = $request->getParam('license_agreement_accepted');
        if ($license !== 'yes') {
            throw new \Exception('You have to agree on license requirements to proceed.');
        }

        //Setting the basePath of Magento appilcation
        $magentoDir   = $request->getParam('magentoDir');
        if ($magentoDir) {
            $this->factoryConfig->setMagentoBasePath(rtrim(str_replace('\\', '/', realpath($magentoDir))), '/');
        } else {
            $this->factoryConfig->setMagentoBasePath();
        }

        //Check File permission
        Helper::checkAndCreateDirectory($this->factoryConfig->getMagentoBasePath() . '/var/cache');
        Helper::checkAndCreateDirectory($this->factoryConfig->getMagentoBasePath() . '/var/log');
        Helper::checkAndCreateDirectory($this->factoryConfig->getMagentoBasePath() . '/var/session');
        $required = $this->filePermission->getRequired();
        $current = $this->filePermission->getCurrent();
        if (array_diff($required, $current)) {
            throw new \Exception('You do no have appropriate file permissions.');
        }

        //Set maintenance mode "on"
        touch($this->factoryConfig->getMagentoBasePath() . '/var/.maintenance.flag');

        //Build all data required for creating local.xml
        $dbHost   = $request->getParam('db_host');
        $dbName   = $request->getParam('db_name');
        $dbUser   = $request->getParam('db_user');
        $dbPass   = $request->getParam('db_pass');
        if (!$dbPass) {
            $dbPass = '';
        }
        $dbPrefix   = $request->getParam('db_table_prefix');
        if (!$dbPrefix) {
            $dbPrefix = '';
        }

        //Check DB connection
        $dbConnectionInfo = array(
            'driver'         => "Pdo",
            'dsn'            => "mysql:dbname=" . $dbName . ";host=" . $dbHost,
            'username'       => $dbUser,
            'password'       => $dbPass,
            'driver_options' => array(
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"
            ),
        );
        $checkDB = new DatabaseCheck($dbConnectionInfo);
        if (!$checkDB->checkConnection()) {
            throw new \Exception('Database connection failure.');
        }

        $adminUrl   = $request->getParam('admin_url');

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

        $this->config->setConfigData($data);
        $this->config->install();

        return  "Completed: Deployment Configuration." . PHP_EOL;
    }

    /**
     * Installs and updates database schema
     * @return string
     * @throws \Exception
     */
    public function installSchemaAction()
    {
        $request = $this->getRequest();
        Helper::checkRequest($request);

        $magentoDir   = $request->getParam('magentoDir');
        if ($magentoDir) {
            $this->factoryConfig->setMagentoBasePath(rtrim(str_replace('\\', '/', realpath($magentoDir))), '/');
        } else {
            $this->factoryConfig->setMagentoBasePath();
        }

        $this->config->loadFromConfigFile();
        $this->setupFactory->setConfig($this->config->getConfigData());

        $moduleNames = array_keys($this->moduleList);
        foreach ($moduleNames as $moduleName) {
            $setup = $this->setupFactory->create($moduleName);
            $setup->applyUpdates();
        }

        foreach ($moduleNames as $moduleName) {
            $setup = $this->setupFactory->create($moduleName);
            $setup->applyRecurringUpdates();
        }

        return  "Completed: Schema Installation." . PHP_EOL;
    }

    /**
     * Installs and updates data
     * @return string
     * @throws \Exception
     */
    public function installDataAction()
    {

        $request = $this->getRequest();
        Helper::checkRequest($request);

        $magentoDir   = $request->getParam('magentoDir');
        if ($magentoDir) {
            $this->factoryConfig->setMagentoBasePath(rtrim(str_replace('\\', '/', realpath($magentoDir))), '/');
        } else {
            $this->factoryConfig->setMagentoBasePath();
        }

        $storeUrl   = $request->getParam('store_url');
        $secureStoreUrl   = $request->getParam('secure_store_url');
        if (!$secureStoreUrl) {
            $secureStoreUrl = false;
        } else {
            if ($secureStoreUrl === 'yes') {
                $secureStoreUrl = true;
            } else {
                $secureStoreUrl = false;
            }
        }
        $secureAdminUrl   = $request->getParam('secure_admin_url');
        if (!$secureAdminUrl) {
            $secureAdminUrl = false;
        } else {
            if ($secureAdminUrl === 'yes') {
                $secureAdminUrl = true;
            } else {
                $secureAdminUrl = false;
            }
        }
        $useRewrites   = $request->getParam('use_rewrites');
        if (!$useRewrites) {
            $useRewrites = false;
        } else {
            if ($useRewrites === 'yes') {
                $useRewrites = true;
            } else {
                $useRewrites = false;
            }
        }
        $encryptionKey   = $request->getParam('encryption_key');
        if (!$encryptionKey) {
            $encryptionKey = md5($this->random->getRandomString(10));
        }
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
                'passwordStatus' => array(
                    'class' => 'weak',
                    'label' => 'Weak',
                ),
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

        $this->config->loadFromConfigFile();
        $this->setupFactory->setConfig($this->config->getConfigData());

        $moduleNames = array_keys($this->moduleList);
        foreach ($moduleNames as $moduleName) {
            $setup = $this->setupFactory->create($moduleName);
        }

        //$this->config->addConfigData($data);
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
            $key = md5($this->random->getRandomString(10));
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

        //Set maintenance mode "off"
        unlink($this->factoryConfig->getMagentoBasePath() . '/var/.maintenance.flag');

        return  "Completed: Data Installation." . PHP_EOL;
    }

    /**
     * IShows necessay information for installing Magento
     * @return string
     * @throws \Exception
     */
    public function infoAction()
    {
        $request = $this->getRequest();
        Helper::checkRequest($request);

        $locale = $request->getParam('locales');
        if ($locale) {
            return  Helper::arrayToString($this->list->getLocaleList());
        }

        $currency = $request->getParam('currencies');
        if ($currency) {
            return  Helper::arrayToString($this->list->getCurrencyList());
        }

        $time = $request->getParam('timezones');
        if ($time) {
            return  Helper::arrayToString($this->list->getTimezoneList());
        }

        $options = $request->getParam('options');
        if ($options) {
            return  Helper::showOptions();
        }
    }
}
