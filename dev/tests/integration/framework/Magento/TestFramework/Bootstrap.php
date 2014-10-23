<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Bootstrap for the integration testing environment
 */
namespace Magento\TestFramework;

class Bootstrap
{
    /**
     * Predefined admin user credentials
     */
    const ADMIN_NAME = 'user';

    const ADMIN_PASSWORD = 'password1';

    /**
     * Predefined admin user role name
     */
    const ADMIN_ROLE_NAME = 'Administrators';

    /**
     * @var \Magento\TestFramework\Bootstrap\Settings
     */
    protected $_settings;

    /**
     * @var string
     */
    protected $_dbVendorName;

    /**
     * @var \Magento\Framework\Simplexml\Element
     */
    protected $dbConfig;

    /**
     * @var \Magento\TestFramework\Application
     */
    protected $_application;

    /**
     * @var \Magento\TestFramework\Bootstrap\Environment
     */
    protected $_envBootstrap;

    /**
     * @var \Magento\TestFramework\Bootstrap\DocBlock
     */
    protected $_docBlockBootstrap;

    /**
     * @var \Magento\TestFramework\Bootstrap\Profiler
     */
    protected $_profilerBootstrap;

    /**
     * @var \Magento\Framework\Shell
     */
    protected $_shell;

    /**
     * Temporary directory to be used to host the application installation sandbox
     *
     * @var string
     */
    protected $_tmpDir;

    /**
     * Constructor
     *
     * @param \Magento\TestFramework\Bootstrap\Settings $settings,
     * @param \Magento\TestFramework\Bootstrap\Environment $envBootstrap,
     * @param \Magento\TestFramework\Bootstrap\DocBlock $docBlockBootstrap,
     * @param \Magento\TestFramework\Bootstrap\Profiler $profilerBootstrap
     * @param \Magento\Framework\Shell $shell
     * @param string $tmpDir
     */
    public function __construct(
        \Magento\TestFramework\Bootstrap\Settings $settings,
        \Magento\TestFramework\Bootstrap\Environment $envBootstrap,
        \Magento\TestFramework\Bootstrap\DocBlock $docBlockBootstrap,
        \Magento\TestFramework\Bootstrap\Profiler $profilerBootstrap,
        \Magento\Framework\Shell $shell,
        $tmpDir
    ) {
        $this->_settings = $settings;
        $this->_envBootstrap = $envBootstrap;
        $this->_docBlockBootstrap = $docBlockBootstrap;
        $this->_profilerBootstrap = $profilerBootstrap;
        $this->_shell = $shell;
        $this->_tmpDir = $tmpDir;
        $this->_application = $this->_createApplication(
            array(
                $this->_settings->getAsConfigFile('TESTS_LOCAL_CONFIG_FILE'),
                $this->_settings->getAsConfigFile('TESTS_LOCAL_CONFIG_EXTRA_FILE')
            ),
            $this->_settings->get('TESTS_GLOBAL_CONFIG_DIR'),
            $this->_settings->getAsMatchingPaths('TESTS_MODULE_CONFIG_FILES'),
            $this->_settings->get('TESTS_MAGENTO_MODE')
        );
    }

    /**
     * Retrieve the application instance
     *
     * @return \Magento\TestFramework\Application
     */
    public function getApplication()
    {
        return $this->_application;
    }

    /**
     * Retrieve the database vendor name
     *
     * @return string
     */
    public function getDbVendorName()
    {
        return $this->_dbVendorName;
    }

    /**
     * Retrieve the database configuration
     *
     * @return \Magento\Framework\Simplexml\Element
     */
    public function getDbConfig()
    {
        return $this->dbConfig;
    }

    /**
     * Perform bootstrap actions required to completely setup the testing environment
     */
    public function runBootstrap()
    {
        $this->_envBootstrap->emulateHttpRequest($_SERVER);
        $this->_envBootstrap->emulateSession($_SESSION);

        $profilerOutputFile = $this->_settings->getAsFile('TESTS_PROFILER_FILE');
        if ($profilerOutputFile) {
            $this->_profilerBootstrap->registerFileProfiler($profilerOutputFile);
        }

        $profilerOutputFile = $this->_settings->getAsFile('TESTS_BAMBOO_PROFILER_FILE');
        $profilerMetricsFile = $this->_settings->getAsFile('TESTS_BAMBOO_PROFILER_METRICS_FILE');
        if ($profilerOutputFile && $profilerMetricsFile) {
            $this->_profilerBootstrap->registerBambooProfiler($profilerOutputFile, $profilerMetricsFile);
        }

        $memoryBootstrap = $this->_createMemoryBootstrap(
            $this->_settings->get('TESTS_MEM_USAGE_LIMIT', 0),
            $this->_settings->get('TESTS_MEM_LEAK_LIMIT', 0)
        );
        $memoryBootstrap->activateStatsDisplaying();
        $memoryBootstrap->activateLimitValidation();

        $this->_docBlockBootstrap->registerAnnotations($this->_application);

        if ($this->_settings->getAsBoolean('TESTS_CLEANUP')) {
            $this->_application->cleanup();
        }
        if ($this->_application->isInstalled()) {
            $this->_application->initialize();
        } else {
            $this->_application->install(self::ADMIN_NAME, self::ADMIN_PASSWORD, self::ADMIN_ROLE_NAME);
        }
    }

    /**
     * Create and return new memory bootstrap instance
     *
     * @param int $memUsageLimit
     * @param int $memLeakLimit
     * @return \Magento\TestFramework\Bootstrap\Memory
     */
    protected function _createMemoryBootstrap($memUsageLimit, $memLeakLimit)
    {
        return new \Magento\TestFramework\Bootstrap\Memory(
            new \Magento\TestFramework\MemoryLimit(
                $memUsageLimit,
                $memLeakLimit,
                new \Magento\TestFramework\Helper\Memory($this->_shell)
            )
        );
    }

    /**
     * Create and return new application instance
     *
     * @param array $localConfigFiles
     * @param string $globalConfigDir
     * @param array $moduleConfigFiles
     * @param string $appMode
     * @return \Magento\TestFramework\Application
     */
    protected function _createApplication(
        array $localConfigFiles,
        $globalConfigDir,
        array $moduleConfigFiles,
        $appMode
    ) {
        $localConfigXml = $this->_loadConfigFiles($localConfigFiles);
        $this->dbConfig = $localConfigXml->connection;
        $this->_dbVendorName = $this->_determineDbVendorName($this->dbConfig);
        $sandboxUniqueId = $this->_calcConfigFilesHash($localConfigFiles);
        $installDir = "{$this->_tmpDir}/sandbox-{$this->_dbVendorName}-{$sandboxUniqueId}";
        $dbClass = 'Magento\TestFramework\Db\\' . ucfirst($this->_dbVendorName);
        /** @var $dbInstance \Magento\TestFramework\Db\AbstractDb */
        $dbInstance = new $dbClass(
            (string)$this->dbConfig->host,
            (string)$this->dbConfig->username,
            (string)$this->dbConfig->password,
            (string)$this->dbConfig->dbName,
            $this->_tmpDir,
            $this->_shell
        );
        return new \Magento\TestFramework\Application(
            $dbInstance,
            $installDir,
            $localConfigXml,
            $globalConfigDir,
            $moduleConfigFiles,
            $appMode
        );
    }

    /**
     * Calculate and return hash of config files' contents
     *
     * @param array $configFiles
     * @return string
     */
    protected function _calcConfigFilesHash($configFiles)
    {
        $result = array();
        foreach ($configFiles as $configFile) {
            $result[] = sha1_file($configFile);
        }
        $result = md5(implode('_', $result));
        return $result;
    }

    /**
     * @param array $configFiles
     * @return \Magento\Framework\Simplexml\Element
     */
    protected function _loadConfigFiles(array $configFiles)
    {
        /** @var $result \Magento\Framework\Simplexml\Element */
        $result = simplexml_load_string('<config/>', 'Magento\Framework\Simplexml\Element');
        foreach ($configFiles as $configFile) {
            /** @var $configXml \Magento\Framework\Simplexml\Element */
            $configXml = simplexml_load_file($configFile, 'Magento\Framework\Simplexml\Element');
            $result->extend($configXml);
        }
        return $result;
    }

    /**
     * Retrieve database vendor name from the database connection XML configuration
     *
     * @param \SimpleXMLElement $dbConfig
     * @return string
     * @throws \Magento\Framework\Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _determineDbVendorName(\SimpleXMLElement $dbConfig)
    {
        $dbVendorAlias = 'mysql4';
        $dbVendorMap = array('mysql4' => 'mysql');
        if (!array_key_exists($dbVendorAlias, $dbVendorMap)) {
            throw new \Magento\Framework\Exception("Database vendor '{$dbVendorAlias}' is not supported.");
        }
        return $dbVendorMap[$dbVendorAlias];
    }
}
