<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Bootstrap for the integration testing environment
 */
class Magento_Test_Bootstrap
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
     * @var Magento_Test_Bootstrap_Settings
     */
    private $_settings;

    /**
     * @var string
     */
    private $_dbVendorName;

    /**
     * @var Magento_Test_Application
     */
    private $_application;

    /**
     * @var Magento_Test_Bootstrap_Environment
     */
    private $_envBootstrap;

    /**
     * @var Magento_Test_Bootstrap_DocBlock
     */
    private $_docBlockBootstrap;

    /**
     * @var Magento_Test_Bootstrap_Profiler
     */
    private $_profilerBootstrap;

    /**
     * @var Magento_Shell
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
     * @param Magento_Test_Bootstrap_Settings $settings
     * @param Magento_Test_Bootstrap_Environment $envBootstrap
     * @param Magento_Test_Bootstrap_DocBlock $docBlockBootstrap
     * @param Magento_Test_Bootstrap_Profiler $profilerBootstrap
     * @param Magento_Shell $shell
     * @param string $tmpDir
     */
    public function __construct(
        Magento_Test_Bootstrap_Settings $settings,
        Magento_Test_Bootstrap_Environment $envBootstrap,
        Magento_Test_Bootstrap_DocBlock $docBlockBootstrap,
        Magento_Test_Bootstrap_Profiler $profilerBootstrap,
        Magento_Shell $shell,
        $tmpDir
    ) {
        $this->_settings = $settings;
        $this->_envBootstrap = $envBootstrap;
        $this->_docBlockBootstrap = $docBlockBootstrap;
        $this->_profilerBootstrap = $profilerBootstrap;
        $this->_shell = $shell;
        $this->_tmpDir = $tmpDir;
        $this->_application = $this->_createApplication(
            $this->_settings->getConfigFiles(
                'TESTS_LOCAL_CONFIG_FILE', 'etc/local-mysql.xml', array('etc/integration-tests-config.xml')
            ),
            $this->_settings->getPathPatternValue('TESTS_GLOBAL_CONFIG_FILES', '../../../app/etc/*.xml'),
            $this->_settings->getPathPatternValue('TESTS_MODULE_CONFIG_FILES', '../../../app/etc/modules/*.xml'),
            $this->_settings->isEnabled('TESTS_MAGENTO_DEVELOPER_MODE'),
            $this->_shell
        );
    }

    /**
     * Retrieve the application instance
     *
     * @return Magento_Test_Application
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
     * Perform bootstrap actions required to completely setup the testing environment
     */
    public function runBootstrap()
    {
        $this->_envBootstrap->emulateHttpRequest($_SERVER);
        $this->_envBootstrap->emulateSession($_SESSION);

        $profilerOutputFile = $this->_settings->getFileValue('TESTS_PROFILER_FILE');
        if ($profilerOutputFile) {
            $this->_profilerBootstrap->registerFileProfiler($profilerOutputFile);
        }

        $profilerOutputFile = $this->_settings->getFileValue('TESTS_BAMBOO_PROFILER_FILE');
        $profilerMetricsFile = $this->_settings->getFileValue('TESTS_BAMBOO_PROFILER_METRICS_FILE');
        if ($profilerOutputFile && $profilerMetricsFile) {
            $this->_profilerBootstrap->registerBambooProfiler($profilerOutputFile, $profilerMetricsFile);
        }

        $memoryBootstrap = $this->_createMemoryBootstrap(
            $this->_settings->getScalarValue('TESTS_MEM_USAGE_LIMIT', 0),
            $this->_settings->getScalarValue('TESTS_MEM_LEAK_LIMIT', 0)
        );
        $memoryBootstrap->activateStatsDisplaying();
        $memoryBootstrap->activateLimitValidation();

        $this->_docBlockBootstrap->registerAnnotations($this->_application);

        if ($this->_settings->isEnabled('TESTS_CLEANUP')) {
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
     * @return Magento_Test_Bootstrap_Memory
     */
    protected function _createMemoryBootstrap($memUsageLimit, $memLeakLimit)
    {
        return new Magento_Test_Bootstrap_Memory(new Magento_Test_MemoryLimit(
            $memUsageLimit, $memLeakLimit, new Magento_Test_Helper_Memory($this->_shell)
        ));
    }

    /**
     * Create and return new application instance
     *
     * @param array $localConfigFiles
     * @param array $globalConfigFiles
     * @param array $moduleConfigFiles
     * @param $isDeveloperMode
     * @return Magento_Test_Application
     */
    protected function _createApplication(
        array $localConfigFiles, array $globalConfigFiles, array $moduleConfigFiles, $isDeveloperMode
    ) {
        $localConfigXml = $this->_loadConfigFiles($localConfigFiles);
        $dbConfig = $localConfigXml->global->resources->default_setup->connection;
        $this->_dbVendorName = $this->_determineDbVendorName($dbConfig);
        $sandboxUniqueId = $this->_calcConfigFilesHash(array_merge(
            $localConfigFiles, $globalConfigFiles, $moduleConfigFiles
        ));
        $installDir = "{$this->_tmpDir}/sandbox-{$this->_dbVendorName}-{$sandboxUniqueId}";
        $dbClass = 'Magento_Test_Db_' . ucfirst($this->_dbVendorName);
        /** @var $dbInstance Magento_Test_Db_DbAbstract */
        $dbInstance = new $dbClass(
            (string)$dbConfig->host,
            (string)$dbConfig->username,
            (string)$dbConfig->password,
            (string)$dbConfig->dbname,
            $this->_tmpDir,
            $this->_shell
        );
        return new Magento_Test_Application(
            $dbInstance, $installDir, $localConfigXml, $globalConfigFiles, $moduleConfigFiles, $isDeveloperMode
        );
    }

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
     * @return Varien_Simplexml_Element
     */
    protected function _loadConfigFiles(array $configFiles)
    {
        /** @var $result Varien_Simplexml_Element */
        $result = simplexml_load_string('<config/>', 'Varien_Simplexml_Element');
        foreach ($configFiles as $configFile) {
            /** @var $configXml Varien_Simplexml_Element */
            $configXml = simplexml_load_file($configFile, 'Varien_Simplexml_Element');
            $result->extend($configXml);
        }
        return $result;
    }

    protected function _determineDbVendorName(SimpleXMLElement $dbConfig)
    {
        $dbVendorAlias = (string)$dbConfig->model;
        $dbVendorMap = array('mysql4' => 'mysql', 'mssql' => 'mssql', 'oracle' => 'oracle');
        if (!array_key_exists($dbVendorAlias, $dbVendorMap)) {
            throw new Magento_Exception("Database vendor '$dbVendorAlias' is not supported.");
        }
        return $dbVendorMap[$dbVendorAlias];
    }
}
