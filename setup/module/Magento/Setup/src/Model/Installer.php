<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Setup\Model;

use Magento\Setup\Module\Setup\ConfigFactory as DeploymentConfigFactory;
use Magento\Config\ConfigFactory as SystemConfigFactory;
use Magento\Config\Config as SystemConfig;
use Magento\Setup\Module\Setup\Config;
use Magento\Setup\Module\SetupFactory;
use Magento\Setup\Module\ModuleListInterface;
use Magento\Store\Model\Store;
use Magento\Framework\Math\Random;
use Magento\Setup\Module\Setup\ConnectionFactory;
use Zend\Db\Sql\Sql;
use Magento\Framework\Shell;
use Magento\Framework\Shell\CommandRenderer;

/**
 * Class Installer contains the logic to install Magento application.
 *
 */
class Installer
{
    /**
     * Parameter indicating command whether to cleanup database in the install routine
     */
    const CLEANUP_DB = 'cleanup_database';

    /**
     * Parameter to specify an order_increment_prefix
     */
    const SALES_ORDER_INCREMENT_PREFIX = 'sales_order_increment_prefix';

    /**#@+
     * Formatting for progress log
     */
    const PROGRESS_LOG_RENDER = '[Progress: %d / %d]';
    const PROGRESS_LOG_REGEX = '/\[Progress: (\d+) \/ (\d+)\]/s';
    /**#@- */

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
     * @var SystemConfig
     */
    private $systemConfig;

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
     * Random Generator
     *
     * @var Random
     */
    private $random;

    /**
     * DB connection factory
     *
     * @var ConnectionFactory
     */
    private $connectionFactory;

    /**
     * Shell executor
     *
     * @var Shell
     */
    private $shell;

    /**
     * Shell command renderer
     *
     * @var CommandRenderer
     */
    private $shellRenderer;

    /**
     * Progress indicator
     *
     * @var Installer\Progress
     */
    private $progress;

    /**
     * Progress indicator factory
     *
     * @var Installer\ProgressFactory
     */
    private $progressFactory;

    /**
     * Constructor
     *
     * @param FilePermissions $filePermissions
     * @param DeploymentConfigFactory $deploymentConfigFactory
     * @param SetupFactory $setupFactory
     * @param ModuleListInterface $moduleList
     * @param SystemConfigFactory $systemConfigFactory
     * @param AdminAccountFactory $adminAccountFactory
     * @param LoggerInterface $log
     * @param Random $random
     * @param ConnectionFactory $connectionFactory
     * @param Installer\ProgressFactory $progressFactory
     */
    public function __construct(
        FilePermissions $filePermissions,
        DeploymentConfigFactory $deploymentConfigFactory,
        SetupFactory $setupFactory,
        ModuleListInterface $moduleList,
        SystemConfigFactory $systemConfigFactory,
        AdminAccountFactory $adminAccountFactory,
        LoggerInterface $log,
        Random $random,
        ConnectionFactory $connectionFactory,
        Installer\ProgressFactory $progressFactory
    ) {
        $this->filePermissions = $filePermissions;
        $this->deploymentConfigFactory = $deploymentConfigFactory;
        $this->setupFactory = $setupFactory;
        $this->moduleList = $moduleList;
        $this->systemConfig = $systemConfigFactory->create();
        $this->adminAccountFactory = $adminAccountFactory;
        $this->log = $log;
        $this->random = $random;
        $this->connectionFactory = $connectionFactory;
        $this->shellRenderer = new CommandRenderer;
        $this->shell = new Shell($this->shellRenderer);
        $this->progressFactory = $progressFactory;
    }

    /**
     * Install Magento application
     *
     * @param \ArrayObject|array $request
     * @return Config
     */
    public function install($request)
    {
        $modulesQty = count($this->moduleList->getModules());
        $this->progress = $this->progressFactory->create([
            10,
            (int)!empty($request[self::CLEANUP_DB]),
            (int)!empty($request[self::SALES_ORDER_INCREMENT_PREFIX]),
            $modulesQty + 1,
        ]);
        $this->log->log('Starting Magento installation:');

        $this->log->log('Enabling Maintenance Mode:');
        $this->setMaintenanceMode(1);
        $this->logProgress();

        $this->log->log('File permissions check...');
        $this->checkFilePermissions();
        $this->logProgress();

        $this->log->log('Installing deployment configuration...');
        $deploymentConfig = $this->installDeploymentConfig($request);
        $this->logProgress();

        if (!empty($request[self::CLEANUP_DB])) {
            $this->log->log('Cleaning up database...');
            $this->cleanupDb($request);
            $this->logProgress();
        }

        $this->log->log('Installing database schema:');
        $this->installSchema();
        $this->logProgress();

        $this->log->log('Installing user configuration...');
        $this->installUserConfig($request);
        $this->logProgress();

        $this->log->log('Installing data fixtures:');
        $this->installDataFixtures();
        $this->logProgress();

        if (!empty($request[self::SALES_ORDER_INCREMENT_PREFIX])) {
            $this->log->log('Creating sales order increment prefix...');
            $this->installOrderIncrementPrefix($request[self::SALES_ORDER_INCREMENT_PREFIX]);
            $this->logProgress();
        }

        $this->log->log('Installing admin user...');
        $this->installAdminUser($request);
        $this->logProgress();

        $this->log->log('Enabling caches:');
        $this->enableCaches();
        $this->logProgress();

        $this->log->log('Disabling Maintenance Mode:');
        $this->setMaintenanceMode(0);
        $this->logProgress();

        $this->log->logSuccess('Magento installation complete.');
        $this->logProgress();
        $this->progress->validateFinished();

        return $deploymentConfig;
    }

    /**
     * Logs progress
     *
     * @return void
     */
    private function logProgress()
    {
        $this->progress->setNext();
        $this->log->logMeta(
            sprintf(self::PROGRESS_LOG_RENDER, $this->progress->getCurrent(), $this->progress->getTotal())
        );
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
        $data[Config::KEY_DATE] = date('r');
        if (empty($data[config::KEY_ENCRYPTION_KEY])) {
            $data[config::KEY_ENCRYPTION_KEY] = md5($this->random->getRandomString(10));
        }
        $config = $this->deploymentConfigFactory->create((array)$data);
        $config->saveToFile();
        return $config;
    }

    /**
     * Installs DB schema
     *
     * @return void
     */
    public function installSchema()
    {
        $moduleNames = array_keys($this->moduleList->getModules());

        $this->log->log('Schema creation/updates:');
        foreach ($moduleNames as $moduleName) {
            $this->log->log("Module '{$moduleName}':");
            $setup = $this->setupFactory->createSetupModule($this->log, $moduleName);
            $setup->applyUpdates();
            $this->logProgress();
        }

        $this->log->log('Schema post-updates:');
        foreach ($moduleNames as $moduleName) {
            $this->log->log("Module '{$moduleName}':");
            $setup = $this->setupFactory->createSetupModule($this->log, $moduleName);
            $setup->applyRecurringUpdates();
        }
        $this->logProgress(); // only once, because this feature is rarely used by any of modules
    }

    /**
     * Installs data fixtures
     *
     * @return void
     * @throws \Exception
     */
    public function installDataFixtures()
    {
        $this->exec('-f %s', [$this->systemConfig->getMagentoBasePath() . '/dev/shell/run_data_fixtures.php']);
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
                    } elseif ((file_exists($result . 'bin/php') && !is_dir($result . 'bin/php'))
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
     * @return void
     */
    public function installUserConfig($data)
    {
        $setup = $this->setupFactory->createSetup($this->log);
        $userConfig = new UserConfigurationData($setup);
        $userConfig->install($data);
    }

    /**
     * Creates store order increment prefix configuration
     *
     * @param string $orderIncrementPrefix Value to use for order increment prefix
     * @return void
     */
    private function installOrderIncrementPrefix($orderIncrementPrefix)
    {
        $setup = $this->setupFactory->createSetup($this->log);
        $dbConnection = $setup->getConnection();

        // get entity_type_id for order
        $select = $dbConnection->select()
            ->from($setup->getTable('eav_entity_type'))
            ->where('entity_type_code = \'order\'');
        $sql = new Sql($dbConnection);
        $selectString = $sql->getSqlStringForSqlObject($select, $dbConnection->getPlatform());
        $statement = $dbConnection->getDriver()->createStatement($selectString);
        $selectResult = $statement->execute();
        $entityTypeId = $selectResult->current()['entity_type_id'];

        // See if row already exists
        $resultSet = $dbConnection->query(
            'SELECT * FROM ' . $setup->getTable('eav_entity_store') . ' WHERE entity_type_id = ? AND store_id = ?',
            [$entityTypeId, Store::DISTRO_STORE_ID]
        );

        if ($resultSet->count() > 0) {
            // row exists, update it
            $entityStoreId = $resultSet->current()->entity_store_id;
            $dbConnection->update(
                $setup->getTable('eav_entity_store'),
                ['increment_prefix' => $orderIncrementPrefix],
                ['entity_store_id' => $entityStoreId]
            );
        } else {
            // add a row to the store's eav table, setting the increment_prefix
            $rowData = [
                'entity_type_id' => $entityTypeId,
                'store_id' => Store::DISTRO_STORE_ID,
                'increment_prefix' => $orderIncrementPrefix,
            ];
            $dbConnection->insert($setup->getTable('eav_entity_store'), $rowData);
        }
    }

    /**
     * Creates admin account
     *
     * @param \ArrayObject|array $data
     * @return void
     */
    public function installAdminUser($data)
    {
        $setup = $this->setupFactory->createSetup($this->log);
        $adminAccount = $this->adminAccountFactory->create($setup, (array)$data);
        $adminAccount->save();
    }

    /**
     * Enables caches after installing application
     *
     * @return void
     */
    private function enableCaches()
    {
        $args = [$this->systemConfig->getMagentoBasePath() . '/dev/shell/cache.php'];
        $this->exec('-f %s -- --set=1', $args);
    }

    /**
     * Enables or disables maintenance mode for Magento application
     *
     * @param int $value
     * @return void
     */
    private function setMaintenanceMode($value)
    {
        $args = [$this->systemConfig->getMagentoBasePath() . '/dev/shell/maintenance.php', $value];
        $this->exec('-f %s -- --set=%s', $args);
    }

    /**
     * Executes a command in CLI
     *
     * @param string $command
     * @param array $args
     * @return void
     */
    private function exec($command, $args)
    {
        $command = self::getPhpExecutablePath() . 'php ' . $command;
        $actualCommand = $this->shellRenderer->render($command, $args);
        $this->log->log($actualCommand);
        $output = $this->shell->execute($command, $args);
        $this->log->log($output);
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
    public function checkDatabaseConnection($dbName, $dbHost, $dbUser, $dbPass = '')
    {
        $adapter = $this->connectionFactory->create([
            Config::KEY_DB_NAME => $dbName,
            Config::KEY_DB_HOST => $dbHost,
            Config::KEY_DB_USER => $dbUser,
            Config::KEY_DB_PASS => $dbPass
        ]);
        $adapter->connect();
        if (!$adapter->getDriver()->getConnection()->isConnected()) {
            throw new \Exception('Database connection failure.');
        }
        return true;
    }

    /**
     * Cleans up database
     *
     * @param \ArrayObject|array $config
     * @return void
     */
    public function cleanupDb($config)
    {
        $adapter = $this->connectionFactory->create($config);
        $dbName = $adapter->quoteIdentifier($config[Config::KEY_DB_NAME]);
        $adapter->query("DROP DATABASE IF EXISTS {$dbName}");
        $adapter->query("CREATE DATABASE IF NOT EXISTS {$dbName}");
    }
}
