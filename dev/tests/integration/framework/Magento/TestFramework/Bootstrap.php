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
    private $_settings;

    /**
     * @var array
     */
    private $installConfig;

    /**
     * @var \Magento\TestFramework\Application
     */
    private $_application;

    /**
     * @var \Magento\TestFramework\Bootstrap\Environment
     */
    private $_envBootstrap;

    /**
     * @var \Magento\TestFramework\Bootstrap\DocBlock
     */
    private $_docBlockBootstrap;

    /**
     * @var \Magento\TestFramework\Bootstrap\Profiler
     */
    private $_profilerBootstrap;

    /**
     * @var \Magento\Framework\Shell
     */
    private $_shell;

    /**
     * Temporary directory to be used to host the application installation sandbox
     *
     * @var string
     */
    private $_tmpDir;

    /**
     * Constructor
     *
     * @param \Magento\TestFramework\Bootstrap\Settings $settings
     * @param \Magento\TestFramework\Bootstrap\Environment $envBootstrap
     * @param \Magento\TestFramework\Bootstrap\DocBlock $docBlockBootstrap
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
            $this->_settings->getAsConfigFile('TESTS_INSTALL_CONFIG_FILE'),
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
     * Retrieve the database configuration
     *
     * @return array
     */
    public function getInstallConfig()
    {
        return $this->installConfig;
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
        if (!$this->_application->isInstalled()) {
            $this->_application->install(self::ADMIN_NAME, self::ADMIN_PASSWORD);
        }
        $this->_application->initialize();
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
     * @param string $installConfigFile
     * @param string $globalConfigDir
     * @param array $moduleConfigFiles
     * @param string $appMode
     * @return \Magento\TestFramework\Application
     */
    protected function _createApplication(
        $installConfigFile,
        $globalConfigDir,
        array $moduleConfigFiles,
        $appMode
    ) {
        $this->installConfig = require $installConfigFile;
        $sandboxUniqueId = sha1_file($installConfigFile);
        $installDir = "{$this->_tmpDir}/sandbox-{$sandboxUniqueId}";
        $dbInstance = new Db\Mysql(
            $this->installConfig['db_host'],
            $this->installConfig['db_user'],
            $this->installConfig['db_pass'],
            $this->installConfig['db_name'],
            $this->_tmpDir,
            $this->_shell
        );
        return new \Magento\TestFramework\Application(
            $dbInstance,
            $this->_shell,
            $installDir,
            $this->installConfig,
            $globalConfigDir,
            $moduleConfigFiles,
            $appMode
        );
    }
}
