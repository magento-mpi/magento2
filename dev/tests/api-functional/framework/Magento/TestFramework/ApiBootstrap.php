<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Bootstrap for the api-functional testing environment
 */
namespace Magento\TestFramework;

class ApiBootstrap extends Bootstrap
{
    /**
     * @var string
     */
    private $applicationInstallDir;

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
        $tmpDir,
        $applicationInstallDir = null
    ) {
        $this->applicationInstallDir = $applicationInstallDir;
        parent::__construct($settings, $envBootstrap, $docBlockBootstrap, $profilerBootstrap, $shell, $tmpDir);
    }

    /**
     * Create and return new application instance
     *
     * @param array $localConfigFiles
     * @param string $globalConfigDir
     * @param array $moduleConfigFiles
     * @param string $appMode
     * @return \Magento\TestFramework\ApiApplication
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
        return new ApiApplication(
            $dbInstance,
            $this->applicationInstallDir,
            $localConfigXml,
            $globalConfigDir,
            $moduleConfigFiles,
            $appMode
        );
    }
}
