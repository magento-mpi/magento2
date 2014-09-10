<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Setup\Model;

use Zend\Stdlib\Parameters;
use Magento\Module\Setup\ConfigFactory as DeploymentConfigFactory;
use Magento\Config\ConfigFactory as SystemConfigFactory;
use Magento\Module\Setup\Config;
use Magento\Module\SetupFactory;
use Magento\Module\ModuleListInterface;

class Installer
{
    /**
     * File permissions checker
     *
     * @var FilePermissions
     */
    private $filePermissions;

    /**
     * Deployment configuration factory
     *
     * @var DeploymentConfigFactory
     */
    private $deploymentConfigFactory;

    /**
     * Resource setup factory
     *
     * @var SetupFactory;
     */
    private $setupFactory;

    /**
     * Module Lists
     *
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * System configuration factory
     *
     * @var SystemConfigFactory
     */
    private $systemConfigFactory;

    /**
     * Admin account factory
     *
     * @var AdminAccountFactory
     */
    private $adminAccountFactory;

    /**
     * Logger
     *
     * @var LoggerInterface
     */
    private $log;

    /**
     * @param FilePermissions $filePermissions
     * @param DeploymentConfigFactory $deploymentConfigFactory
     * @param SetupFactory $setupFactory
     * @param ModuleListInterface $moduleList
     * @param SystemConfigFactory $systemConfigFactory
     * @param AdminAccountFactory $adminAccountFactory
     * @param LoggerInterface $log
     */
    public function __construct(
        FilePermissions $filePermissions,
        DeploymentConfigFactory $deploymentConfigFactory,
        SetupFactory $setupFactory,
        ModuleListInterface $moduleList,
        SystemConfigFactory $systemConfigFactory,
        AdminAccountFactory $adminAccountFactory,
        LoggerInterface $log
    ) {
        $this->filePermissions = $filePermissions;
        $this->deploymentConfigFactory = $deploymentConfigFactory;
        $this->setupFactory = $setupFactory;
        $this->moduleList = $moduleList;
        $this->systemConfigFactory = $systemConfigFactory;
        $this->adminAccountFactory = $adminAccountFactory;
        $this->log = $log;
    }

    /**
     * Install Magento application
     *
     * @param \ArrayObject|array $deploymentConfigData
     * @param \ArrayObject|array $userConfigData
     * @param \ArrayObject|array $adminUserData
     * @return Config
     */
    public function install($deploymentConfigData, $userConfigData, $adminUserData)
    {
        $this->log->log('Starting Magento installation:');

        $this->log->log('File permissions check...');
        $this->checkFilePermissions();

        $this->log->log('Installing deployment configuration...');
        $deploymentConfig = $this->installDeploymentConfig($deploymentConfigData);

        $this->log->log('Installing database schema:');
        $this->installSchema();

        $this->log->log('Installing user configuration...');
        $this->installUserConfig($userConfigData);

        $this->log->log('Installing data fixtures:');
        $this->installDataFixtures();

        $this->log->log('Installing admin user...');
        $this->installAdminUser($adminUserData);

        $this->log->logSuccess('Magento installation complete.');

        return $deploymentConfig;
    }

    /**
     * Check permissions of directories that are expected to be writable
     *
     * @return void
     * @throws \Exception
     */
    public function checkFilePermissions()
    {
        $results = $this->filePermissions->checkPermission();
        if ($results) {
            $errorMsg = 'Missing writing permissions to the following directories: ';
            foreach ($results as $result) {
                $errorMsg .= '\'' . $result . '\' ';
            }
            throw new \Exception($errorMsg);
        }
    }

    /**
     * Installs deployment configuration
     *
     * @param \ArrayObject|array $data
     * @return Config
     */
    public function installDeploymentConfig($data)
    {
        $config = $this->deploymentConfigFactory->create($data);
        $config->install();
        return $config;
    }

    /**
     * Installs DB schema
     */
    public function installSchema()
    {
        $moduleNames = array_keys($this->moduleList->getModules());

        $this->log->log('Schema creation/updates:');
        foreach ($moduleNames as $moduleName) {
            $this->log->log("Module '{$moduleName}':");
            $setup = $this->setupFactory->createSetupModule($this->log, $moduleName);
            $setup->applyUpdates();
        }

        $this->log->log('Schema post-updates:');
        foreach ($moduleNames as $moduleName) {
            $this->log->log("Module '{$moduleName}':");
            $setup = $this->setupFactory->createSetupModule($this->log, $moduleName);
            $setup->applyRecurringUpdates();
        }
    }

    /**
     * Installs data fixtures
     *
     * @throws \Exception
     */
    public function installDataFixtures()
    {
        $systemConfig = $this->systemConfigFactory->create();
        $phpPath = self::getPhpExecutablePath();
        $command = $phpPath . 'php -f ' . $systemConfig->getMagentoBasePath() . '/dev/shell/run_data_fixtures.php 2>&1';
        $this->log->log($command);
        exec($command, $output, $exitCode);
        if ($exitCode !== 0) {
            $outputMsg = implode(PHP_EOL, $output);
            throw new \Exception('exec() returned error [' . $exitCode . ']' . PHP_EOL . $outputMsg);
        }
    }

    /**
     * Finds the executable path for PHP
     *
     * @return string
     * @throws \Exception
     */
    private static function getPhpExecutablePath()
    {
        $result = '';
        $iniFile = fopen(php_ini_loaded_file(), 'r');
        while ($line = fgets($iniFile)) {
            if ((strpos($line, 'extension_dir') !== false) && (strrpos($line, ";") !==0)) {
                $extPath = explode("=", $line);
                $pathFull = explode("\"", $extPath[1]);
                $pathParts = str_replace('\\', '/', $pathFull[1]);
                foreach (explode('/', $pathParts) as $piece) {
                    if ((file_exists($result . 'php') && !is_dir($result . 'php'))
                        || (file_exists($result . 'php.exe') && !is_dir($result . 'php.exe'))) {
                        break;
                    } else if ((file_exists($result . 'bin/php') && !is_dir($result . 'bin/php'))
                        || (file_exists($result . 'bin/php.exe') && !is_dir($result . 'bin/php.exe'))) {
                        $result .= 'bin' . '/';
                        break;
                    } else {
                        $result .= $piece . '/';
                    }
                }
                break;
            }
        }
        fclose($iniFile);
        return $result;
    }

    /**
     * Installs user configuration
     *
     * @param \ArrayObject|array $data
     */
    public function installUserConfig($data)
    {
        $setup = $this->setupFactory->createSetup($this->log);
        $userConfig = new UserConfigurationData($setup);
        $userConfig->install($data);
    }

    /**
     * Creates admin account
     *
     * @param \ArrayObject|array $data
     */
    public function installAdminUser($data)
    {
        $setup = $this->setupFactory->createSetup($this->log);
        $adminAccount = $this->adminAccountFactory->create($setup, (array)$data);
        $adminAccount->save();
    }

    /**
     * Checks Database Connection
     *
     * @param string $dbName
     * @param string $dbHost
     * @param string $dbUser
     * @param string $dbPass
     * @return boolean
     * @throws \Exception
     */
    public static function checkDatabaseConnection($dbName, $dbHost, $dbUser, $dbPass = '')
    {
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
        return true;
    }
}
