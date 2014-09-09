<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Setup\Controller;

use Magento\Framework\Math\Random;
use Magento\Config\ConfigFactory;
use Magento\Module\ModuleListInterface;
use Magento\Framework\DB\Adapter\Pdo\Mysql;
use Magento\Module\SetupFactory;
use Magento\Module\Setup\Config;
use Magento\Setup\Helper\Helper;
use Magento\Config\Config as SystemConfig;
use Magento\Setup\Model\LoggerInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Magento\Setup\Model\AdminAccountFactory;
use Zend\EventManager\EventManagerInterface;
use Zend\Stdlib\RequestInterface as Request;
use Symfony\Component\Process\PhpExecutableFinder;
use Magento\Setup\Model\UserConfigurationDataFactory;

/**
 * Abstract Controller for installation
 *
 * @package Magento\Setup\Controller
 */
class AbstractInstallActionController extends AbstractActionController
{

    /**
     * Configurations
     *
     * @var Config
     */
    protected $config;

    /**
     * Factory Configurations
     *
     * @var SystemConfig
     */
    protected $systemConfig;

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
     * Module Lists
     *
     * @var ModuleListInterface
     */
    protected $moduleList;

    /**
     * PHP executable finder
     *
     * @var PhpExecutableFinder
     */
    protected $phpExecutableFinder;

    /**
     * Default Controller
     *
     * @param ModuleListInterface $moduleList
     * @param SetupFactory $setupFactory
     * @param AdminAccountFactory $adminAccountFactory
     * @param Random $random
     * @param Config $config
     * @param ConfigFactory $systemConfigFactory
     * @param UserConfigurationDataFactory $userConfigurationDataFactory
     * @param LoggerInterface $loggerInterface
     * @param PhpExecutableFinder $phpExecutableFinder
     */
    public function __construct(
        ModuleListInterface $moduleList,
        SetupFactory $setupFactory,
        AdminAccountFactory $adminAccountFactory,
        Random $random,
        Config $config,
        ConfigFactory $systemConfigFactory,
        UserConfigurationDataFactory $userConfigurationDataFactory,
        LoggerInterface $loggerInterface,
        PhpExecutableFinder $phpExecutableFinder
    ) {
        $this->moduleList = $moduleList;
        $this->setupFactory = $setupFactory;
        $this->config = $config;
        $this->systemConfig = $systemConfigFactory->create();
        $this->adminAccountFactory = $adminAccountFactory;
        $this->random = $random;
        $this->userConfigurationDataFactory = $userConfigurationDataFactory;
        $this->logger = $loggerInterface;
        $this->phpExecutableFinder = $phpExecutableFinder;
    }

    /**
     * Installs Deployment Configuration
     *
     * @param array $data
     * @return string
     */
    public function installDeploymentConfiguration($data)
    {
        $this->config->setConfigData($this->config->convertFromDataObject($data));

        //Creates Deployment Configuration
        $this->config->install();

        $key = $data['config']['encrypt']['key'];
        $this->config->replaceTmpEncryptKey($key);
        $this->config->replaceTmpInstallDate(date('r'));

        return $key;
    }

    /**
     * Installs User Configuration Data
     *
     * @param array $data
     * @return void
     */
    protected function installUserConfigurationData($data)
    {
        //Loading Configurations
        $this->config->setConfigData($this->config->convertFromDataObject($data));
        $this->config->addConfigData($this->config->getConfigurationFromDeploymentFile());
        $setup = $this->setupFactory->setConfig($this->config->getConfigData())->create();
        $this->userConfigurationDataFactory->setConfig($this->config->getConfigData());
        $this->userConfigurationDataFactory->create($setup)->install($data);

        // Create administrator account
        $this->adminAccountFactory->setConfig($this->config->getConfigData());
        $adminAccount = $this->adminAccountFactory->create($setup);
        $adminAccount->save();
        $this->logger->log("Completed: User Configuration.");

    }

    /**
     * Installs Schema Updates
     *
     * @return void
     */
    protected function installSchemaUpdates()
    {
        $this->setupFactory->setConfig($this->config->getConfigData());

        //List of All Module Names
        $moduleNames = array_keys($this->moduleList->getModules());

        // Do schema updates for each module
        foreach ($moduleNames as $moduleName) {
            $setup = $this->setupFactory->create($moduleName);
            $setup->applyUpdates();
            $this->logger->logSuccess($moduleName);
        }
        $this->logger->log("Installing recurring post-schema updates for all modules.");

        // Do post-schema updates for each module
        foreach ($moduleNames as $moduleName) {
            $setup = $this->setupFactory->create($moduleName);
            $setup->applyRecurringUpdates();
        }
        $this->logger->log("Completed: Post-Schema Updates.");

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
            ' -f ' . $this->systemConfig->getMagentoBasePath() . '/dev/shell/run_data_fixtures.php',
            $output,
            $exitCode
        );
        if ($exitCode !== 0) {
            $outputMsg = implode(PHP_EOL, $output);
            throw new \Exception('Data Update Failed with Exit Code: ' . $exitCode . PHP_EOL . $outputMsg);
        } else {
            $this->logger->log("Completed: Data Updates");
        }
        return $exitCode;
    }

    /**
     * Updates Magento Directory
     *
     * @param string $magentoDir
     * @return void
     */
    protected function updateMagentoDirectory($magentoDir)
    {
        if ($magentoDir) {
            $this->systemConfig->setMagentoBasePath(rtrim(str_replace('\\', '/', realpath($magentoDir))), '/');
        } else {
            $this->systemConfig->setMagentoBasePath();
        }
    }

    /**
     * Creates Random Encryption Key
     *
     * @return string
     */
    protected final function getRandomEncryptionKey()
    {
        return md5($this->random->getRandomString(10));
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
} 