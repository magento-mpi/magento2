<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Controller;

use Magento\Config\ConfigFactory;
use Magento\Framework\DB\Adapter\Pdo\Mysql;
use Magento\Framework\Math\Random;
use Magento\Locale\Lists;
use Magento\Module\Setup\Config;
use Magento\Setup\Helper\Helper;
use Magento\Setup\Model\DatabaseCheck;
use Magento\Setup\Model\FilePermissions;
use Zend\Mvc\Controller\AbstractActionController;

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
     * @param \Magento\Setup\Model\FilePermissions $filePermission
     * @param \Magento\Locale\Lists $list
     * @param ConfigFactory $configFactory
     * @param Random $random
     * @param Config $config
     */
    public function __construct(
        FilePermissions $filePermission,
        Lists $list,
        ConfigFactory $configFactory,
        Random $random,
        Config $config
    ) {
        $this->filePermission = $filePermission;
        $this->list = $list;
        $this->configFactory = $configFactory;
        $this->factoryConfig = $this->configFactory->create();
        $this->random = $random;
        $this->config = $config;
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

        $storeUrl   = $request->getParam('store_url');
        $adminUrl   = $request->getParam('admin_url');
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
            'db' => array(
                'useExistingDB' => 1,
                'useAccess' => 1,
                'user' => $dbUser,
                'password' => $dbPass,
                'host' => $dbHost,
                'name' => $dbName,
                'tablePrefix' => $dbPrefix,
            ),
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
                    'admin' => $adminUrl,
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

        $this->config->setConfigData($data);
        $this->config->install();

        /********************** Here goes schema updater (for one install step) **********************/

        $this->config->replaceTmpEncryptKey($encryptionKey);
        $this->config->replaceTmpInstallDate(date('r'));

        /********************** Here goes data updater (for one install step) **********************/

        //Set maintenance mode "off"
        unlink($this->factoryConfig->getMagentoBasePath() . '/var/.maintenance.flag');

        return  "local.xml file has been created successfully." . PHP_EOL;
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

        return  "Schema have been installed successfully." . PHP_EOL;
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

        return  "Data have been installed successfully." . PHP_EOL;
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

        return "Wrong command!";
    }
}
