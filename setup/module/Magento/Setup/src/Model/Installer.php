<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Setup\Model;

use Magento\Framework\App\DeploymentConfig\Writer;
use Magento\Setup\Module\SetupFactory;
use Magento\Framework\Module\ModuleList;
use Magento\Framework\Module\ModuleList\Loader as ModuleLoader;
use Magento\Framework\Module\ModuleList\DeploymentConfig;
use Magento\Store\Model\Store;
use Magento\Framework\Math\Random;
use Magento\Framework\App\Resource\ConnectionFactory;
use Magento\Framework\Shell;
use Magento\Framework\Shell\CommandRenderer;
use Symfony\Component\Process\PhpExecutableFinder;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\MaintenanceMode;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\FilesystemException;
use Zend\ServiceManager\ServiceLocatorInterface;
use Magento\Setup\Mvc\Bootstrap\InitParamListener;
use Magento\Framework\App\DeploymentConfig\BackendConfig;
use Magento\Framework\App\DeploymentConfig\DbConfig;
use Magento\Framework\App\DeploymentConfig\EncryptConfig;
use Magento\Framework\App\DeploymentConfig\InstallConfig;
use Magento\Framework\App\DeploymentConfig\SessionConfig;
use Magento\Framework\App\DeploymentConfig\ResourceConfig;

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

    /**#@+
     * Parameters for enabling/disabling modules
     */
    const ENABLE_MODULES = 'enable_modules';
    const DISABLE_MODULES = 'disable_modules';
    /**#@- */

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

    const INFO_MESSAGE = 'message';

    /**
     * File permissions checker
     *
     * @var FilePermissions
     */
    private $filePermissions;

    /**
     * Deployment configuration repository
     *
     * @var Writer
     */
    private $deploymentConfigWriter;

    /**
     * Resource setup factory
     *
     * @var SetupFactory;
     */
    private $setupFactory;

    /**
     * Module list
     *
     * @var ModuleList
     */
    private $moduleList;

    /**
     * Module list loader
     *
     * @var ModuleLoader
     */
    private $moduleLoader;

    /**
     * List of directories of Magento application
     *
     * @var DirectoryList
     */
    private $directoryList;

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
     * Maintenance mode handler
     *
     * @var MaintenanceMode
     */
    private $maintenanceMode;

    /**
     * Magento filesystem
     *
     * @var Filesystem
     */
    private $filesystem;

    /**
     * Installation information
     *
     * @var array
     */
    private $installInfo = array();

    /**
     * A materialized string of initialization parameters to pass on any script that's run externally by this model
     *
     * @var string
     */
    private $execParams;

    /**
     * Constructor
     *
     * @param FilePermissions $filePermissions
     * @param Writer $deploymentConfigWriter
     * @param SetupFactory $setupFactory
     * @param ModuleList $moduleList
     * @param ModuleLoader $moduleLoader
     * @param DirectoryList $directoryList
     * @param AdminAccountFactory $adminAccountFactory
     * @param LoggerInterface $log
     * @param Random $random
     * @param ConnectionFactory $connectionFactory
     * @param MaintenanceMode $maintenanceMode
     * @param Filesystem $filesystem
     * @param ServiceLocatorInterface $serviceManager
     */
    public function __construct(
        FilePermissions $filePermissions,
        Writer $deploymentConfigWriter,
        SetupFactory $setupFactory,
        ModuleList $moduleList,
        ModuleLoader $moduleLoader,
        DirectoryList $directoryList,
        AdminAccountFactory $adminAccountFactory,
        LoggerInterface $log,
        Random $random,
        ConnectionFactory $connectionFactory,
        MaintenanceMode $maintenanceMode,
        Filesystem $filesystem,
        ServiceLocatorInterface $serviceManager
    ) {
        $this->filePermissions = $filePermissions;
        $this->deploymentConfigWriter = $deploymentConfigWriter;
        $this->setupFactory = $setupFactory;
        $this->moduleList = $moduleList;
        $this->moduleLoader = $moduleLoader;
        $this->directoryList = $directoryList;
        $this->adminAccountFactory = $adminAccountFactory;
        $this->log = $log;
        $this->random = $random;
        $this->connectionFactory = $connectionFactory;
        $this->shellRenderer = new CommandRenderer;
        $this->shell = new Shell($this->shellRenderer);
        $this->maintenanceMode = $maintenanceMode;
        $this->filesystem = $filesystem;
        $this->execParams = urldecode(http_build_query($serviceManager->get(InitParamListener::BOOTSTRAP_PARAM)));
        $this->installInfo[self::INFO_MESSAGE] = array();
    }

    /**
     * Install Magento application
     *
     * @param \ArrayObject|array $request
     * @return void
     * @throws \LogicException
     */
    public function install($request)
    {
        $script[] = ['File permissions check...', 'checkInstallationFilePermissions', []];
        $script[] = ['Enabling Maintenance Mode...', 'setMaintenanceMode', [1]];
        $script[] = ['Installing deployment configuration...', 'installDeploymentConfig', [$request]];
        if (!empty($request[self::CLEANUP_DB])) {
            $script[] = ['Cleaning up database...', 'cleanupDb', []];
        }
        $script[] = ['Installing database schema:', 'installSchema', []];
        $script[] = ['Installing user configuration...', 'installUserConfig', [$request]];
        $script[] = ['Installing data fixtures:', 'installDataFixtures', []];
        if (!empty($request[self::SALES_ORDER_INCREMENT_PREFIX])) {
            $script[] = [
                'Creating sales order increment prefix...',
                'installOrderIncrementPrefix',
                [$request[self::SALES_ORDER_INCREMENT_PREFIX]]
            ];
        }
        $script[] = ['Installing admin user...', 'installAdminUser', [$request]];
        $script[] = ['Enabling caches:', 'enableCaches', []];
        $script[] = ['Disabling Maintenance Mode:', 'setMaintenanceMode', [0]];
        $script[] = ['Post installation file permissions check...', 'checkApplicationFilePermissions', []];

        $estimatedModules = $this->createModulesConfig($request);
        $total = count($script) + count($estimatedModules->getData());
        $this->progress = new Installer\Progress($total, 0);

        $this->log->log('Starting Magento installation:');

        while (list(, list($message, $method, $params)) = each($script)) {
            $this->log->log($message);
            call_user_func_array([$this, $method], $params);
            $this->logProgress();
        }

        $this->log->logSuccess('Magento installation complete.');

        if ($this->progress->getCurrent() != $this->progress->getTotal()) {
            throw new \LogicException('Installation progress did not finish properly.');
        }
    }

    /**
     * Creates modules deployment configuration segment
     *
     * @param \ArrayObject|array $request
     * @return DeploymentConfig
     * @throws \LogicException
     */
    private function createModulesConfig($request)
    {
        $all = array_keys($this->moduleLoader->load());
        $enable = $this->readListOfModules($all, $request, self::ENABLE_MODULES) ?: $all;
        $disable = $this->readListOfModules($all, $request, self::DISABLE_MODULES);
        $toEnable = array_diff($enable, $disable);
        if (empty($toEnable)) {
            throw new \LogicException('Unable to determine list of enabled modules.');
        }
        $result = [];
        foreach ($all as $module) {
            $key = array_search($module, $toEnable);
            $result[$module] = false !== $key;
        }
        return new DeploymentConfig($result);
    }

    /**
     * Creates backend deployment configuration segment
     *
     * @param \ArrayObject|array $data
     * @return \Magento\Framework\App\DeploymentConfig\SegmentInterface
     */
    private function createBackendConfig($data)
    {
        $backendConfigData = array(BackendConfig::KEY_FRONTNAME => $data[BackendConfig::KEY_BACKEND_FRONTNAME]);
        return new BackendConfig($backendConfigData);
    }

    /**
     * Creates encrypt deployment configuration segment
     *
     * @param \ArrayObject|array $data
     * @return \Magento\Framework\App\DeploymentConfig\SegmentInterface
     */
    private function createEncryptConfig($data)
    {
        $cryptConfigData = array(EncryptConfig::KEY_ENCRYPTION_KEY => $data[EncryptConfig::KEY_ENCRYPTION_KEY]);
        return new EncryptConfig($cryptConfigData);
    }

    /**
     * Creates db deployment configuration segment
     *
     * @param \ArrayObject|array $data
     * @return \Magento\Framework\App\DeploymentConfig\SegmentInterface
     */
    private function createDbConfig($data)
    {
        $dbConfigData = array(
            DbConfig::KEY_HOST => $data[DbConfig::KEY_DB_HOST],
            DbConfig::KEY_INIT_STATEMENTS =>
                isset($data[DbConfig::KEY_DB_INIT_STATEMENTS]) ? $data[DbConfig::KEY_DB_INIT_STATEMENTS] : null,
            DbConfig::KEY_MODEL => isset($data[DbConfig::KEY_DB_MODEL]) ? $data[DbConfig::KEY_DB_MODEL] : null,
            DbConfig::KEY_NAME => $data[DbConfig::KEY_DB_NAME],
            DbConfig::KEY_PASS => isset($data[DbConfig::KEY_DB_PASS]) ? $data[DbConfig::KEY_DB_PASS] : null,
            DbConfig::KEY_PREFIX => isset($data[DbConfig::KEY_DB_PREFIX]) ? $data[DbConfig::KEY_DB_PREFIX] : null,
            DbConfig::KEY_USER => $data[DbConfig::KEY_DB_USER],
        );
        return new DbConfig($dbConfigData);
    }

    /**
     * Creates session deployment configuration segment
     *
     * @param \ArrayObject|array $data
     * @return \Magento\Framework\App\DeploymentConfig\SegmentInterface
     */
    private function createSessionConfig($data)
    {
        $sessionConfigData = array(SessionConfig::KEY_SAVE =>
            isset($data[SessionConfig::KEY_SESSION_SAVE]) ? $data[SessionConfig::KEY_SESSION_SAVE] : null);
        return new SessionConfig($sessionConfigData);
    }

    /**
     * Creates install deployment configuration segment
     *
     * @param \ArrayObject|array $data
     * @return \Magento\Framework\App\DeploymentConfig\SegmentInterface
     */
    private function createInstallConfig($data)
    {
        $installConfigData = array(InstallConfig::KEY_DATE => $data[InstallConfig::KEY_DATE]);
        return new InstallConfig($installConfigData);
    }

    /**
     * Determines list of modules from request based on list of all modules
     *
     * @param string[] $all
     * @param array $request
     * @param string $key
     * @return string[]
     * @throws \LogicException
     */
    private function readListOfModules($all, $request, $key)
    {
        $result = [];
        if (!empty($request[$key])) {
            if ($request[$key] == 'all') {
                $result = $all;
            } else {
                $result = explode(',', $request[$key]);
                foreach ($result as $module) {
                    if (!in_array($module, $all)) {
                        throw new \LogicException("Unknown module in the requested list: '{$module}'");
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Logs progress
     *
     * @return void
     */
    private function logProgress()
    {
        if (!$this->progress) {
            return;
        }
        $this->progress->setNext();
        $this->log->logMeta(
            sprintf(self::PROGRESS_LOG_RENDER, $this->progress->getCurrent(), $this->progress->getTotal())
        );
    }

    /**
     * Check permissions of directories that are expected to be writable for installation
     *
     * @return void
     * @throws \Exception
     */
    public function checkInstallationFilePermissions()
    {
        $results = $this->filePermissions->getMissingWritableDirectoriesForInstallation();
        if ($results) {
            $errorMsg = 'Missing writing permissions to the following directories: ';
            foreach ($results as $result) {
                $errorMsg .= '\'' . $result . '\' ';
            }
            throw new \Exception($errorMsg);
        }
    }

    /**
     * Check permissions of directories that are expected to be non-writable for application
     *
     * @return void
     */
    public function checkApplicationFilePermissions()
    {
        $results = $this->filePermissions->getUnnecessaryWritableDirectoriesForApplication();
        if ($results) {
            $errorMsg = 'For security, remove write permissions from these directories: ';
            foreach ($results as $result) {
                $errorMsg .= '\'' . $result . '\' ';
            }
            $this->log->log($errorMsg);
            $this->installInfo[self::INFO_MESSAGE][] = $errorMsg;
        }
    }

    /**
     * Installs deployment configuration
     *
     * @param \ArrayObject|array $data
     * @return void
     */
    public function installDeploymentConfig($data)
    {
        $data[InstallConfig::KEY_DATE] = date('r');
        if (empty($data[EncryptConfig::KEY_ENCRYPTION_KEY])) {
            $data[EncryptConfig::KEY_ENCRYPTION_KEY] = md5($this->random->getRandomString(10));
        }
        $this->installInfo[EncryptConfig::KEY_ENCRYPTION_KEY] = $data[EncryptConfig::KEY_ENCRYPTION_KEY];

        $configs = [
            $this->createBackendConfig($data),
            $this->createDbConfig($data),
            $this->createEncryptConfig($data),
            $this->createInstallConfig($data),
            $this->createSessionConfig($data),
            new ResourceConfig(),
            $this->createModulesConfig($data),
        ];
        $this->deploymentConfigWriter->create($configs);
    }

    /**
     * Installs DB schema
     *
     * @return void
     */
    public function installSchema()
    {
        $moduleNames = $this->moduleList->getNames();

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
    }

    /**
     * Installs data fixtures
     *
     * @return void
     * @throws \Exception
     */
    public function installDataFixtures()
    {
        $params = [$this->directoryList->getRoot() . '/dev/shell/run_data_fixtures.php', $this->execParams];
        $this->exec('-f %s -- --bootstrap=%s', $params);
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
        $configData = $userConfig->getConfigData($data);
        $paramsString = '-f %s --';
        $paramsArray = [$this->directoryList->getRoot() . '/dev/shell/user_config_data.php'];
        $paramsString .= ' --noOfConfigDatasets=%s';
        if (count($configData) === 0) {
            return;
        } else {
            $paramsArray[] = count($configData);
        }
        foreach ($configData as $path => $val) {
            $paramsString .= ' --website=%s --store=%s --path=%s --value=%s';
            $paramsArray[] = "0";
            $paramsArray[] = "0";
            $paramsArray[] = $path;
            $paramsArray[] = $val;
        }
        $this->exec($paramsString, $paramsArray);
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
            ->from($setup->getTable('eav_entity_type'), 'entity_type_id')
            ->where('entity_type_code = \'order\'');
        $entityTypeId = $dbConnection->fetchOne($select);

        // See if row already exists
        $incrementRow = $dbConnection->fetchRow(
            'SELECT * FROM ' . $setup->getTable('eav_entity_store') . ' WHERE entity_type_id = ? AND store_id = ?',
            [$entityTypeId, Store::DISTRO_STORE_ID]
        );

        if (!empty($incrementRow)) {
            // row exists, update it
            $entityStoreId = $incrementRow['entity_store_id'];
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
     * Uninstall Magento application
     *
     * @return void
     */
    public function uninstall()
    {
        $this->log->log('Starting Magento uninstallation:');

        $this->cleanupDb();
        $this->log->log('File system cleanup:');
        $this->deleteDirContents(DirectoryList::VAR_DIR);
        $this->deleteDirContents(DirectoryList::STATIC_VIEW);
        $this->deleteDeploymentConfig();

        $this->log->logSuccess('Magento uninstallation complete.');
    }

    /**
     * Enables caches after installing application
     *
     * @return void
     */
    private function enableCaches()
    {
        $args = [$this->directoryList->getRoot() . '/dev/shell/cache.php', $this->execParams];
        $this->exec('-f %s -- --set=1 --bootstrap=%s', $args);
    }

    /**
     * Enables or disables maintenance mode for Magento application
     *
     * @param int $value
     * @return void
     */
    private function setMaintenanceMode($value)
    {
        $this->maintenanceMode->set($value);
    }

    /**
     * Executes a command in CLI
     *
     * @param string $command
     * @param array $args
     * @return void
     * @throws \Exception
     */
    private function exec($command, $args)
    {
        $phpFinder = new PhpExecutableFinder();
        $phpPath = $phpFinder->find();
        if (!$phpPath) {
            throw new \Exception(
                'Cannot find PHP executable path.'
                . ' Please set $PATH environment variable to include the full path of the PHP executable'
            );
        }
        $command = $phpPath . ' ' . $command;
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
        $connection = $this->connectionFactory->create([
            'dbname' => $dbName,
            'host' => $dbHost,
            'username' => $dbUser,
            'password' => $dbPass,
            'active' => true,
        ]);

        if (!$connection) {
            throw new \Exception('Database connection failure.');
        }
        return true;
    }

    /**
     * Return messages
     *
     * @return array
     */
    public function getInstallInfo()
    {
        return $this->installInfo;
    }


    /**
     * Deletes the database and creates it again
     *
     * @return void
     */
    private function cleanupDb()
    {
        // stops cleanup if app/etc/config.php does not exist
        if ($this->filesystem->getDirectoryWrite(DirectoryList::CONFIG)->isFile('config.php')) {
            $reader = new \Magento\Framework\App\DeploymentConfig\Reader($this->directoryList);
            $deploymentConfig = new \Magento\Framework\App\DeploymentConfig($reader, []);
            $config = $deploymentConfig->getConnection(\Magento\Framework\App\Resource\Config::DEFAULT_SETUP_CONNECTION);
            if ($config) {
                try {
                    $connection = $this->connectionFactory->create($config);
                    if (!$connection) {
                        $this->log->log("Can't create connection to database - skipping database cleanup");
                    }
                } catch (\Exception $e) {
                    $this->log->log($e->getMessage() . ' - skipping database cleanup');
                    return;
                }
                $dbName = $connection->quoteIdentifier($config['dbname']);
                $this->log->log("Recreating database {$dbName}");
                $connection->query("DROP DATABASE IF EXISTS {$dbName}");
                $connection->query("CREATE DATABASE IF NOT EXISTS {$dbName}");
                return;
            }
        }
        $this->log->log('No database connection defined - skipping database cleanup');
    }

    /**
     * Removes contents of a directory
     *
     * @param string $type
     * @return void
     */
    private function deleteDirContents($type)
    {
        $dir = $this->filesystem->getDirectoryWrite($type);
        $dirPath = $dir->getAbsolutePath();
        if (!$dir->isExist()) {
            $this->log->log("The directory '{$dirPath}' doesn't exist - skipping cleanup");
            return;
        }
        foreach ($dir->read() as $path) {
            if (preg_match('/^\./', $path)) {
                continue;
            }
            $this->log->log("{$dirPath}{$path}");
            try {
                $dir->delete($path);
            } catch (FilesystemException $e) {
                $this->log->log($e->getMessage());
            }
        }
    }

    /**
     * Removes deployment configuration
     *
     * @return void
     */
    private function deleteDeploymentConfig()
    {
        $configDir = $this->filesystem->getDirectoryWrite(DirectoryList::CONFIG);
        $file = 'config.php';
        $absolutePath = $configDir->getAbsolutePath($file);
        if (!$configDir->isFile($file)) {
            $this->log->log("The file '{$absolutePath}' doesn't exist - skipping cleanup");
            return;
        }
        try {
            $this->log->log($absolutePath);
            $configDir->delete($file);
        } catch (FilesystemException $e) {
            $this->log->log($e->getMessage());
        }
    }
}
