<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestFramework;

use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

class ApiApplication extends Application
{
    /**
     * Constructor
     *
     * @param \Magento\TestFramework\Db\AbstractDb $dbInstance
     * @param string $installDir
     * @param \Magento\Framework\Simplexml\Element $localXml
     * @param $globalConfigDir
     * @param array $moduleEtcFiles
     * @param string $appMode
     */
    public function __construct(
        \Magento\TestFramework\Db\AbstractDb $dbInstance,
        $installDir,
        \Magento\Framework\Simplexml\Element $localXml,
        $globalConfigDir,
        array $moduleEtcFiles,
        $appMode
    ) {
        $this->_db = $dbInstance;
        $this->_localXml = $localXml;
        $this->_globalConfigDir = realpath($globalConfigDir);
        $this->_moduleEtcFiles = $moduleEtcFiles;
        $this->_appMode = $appMode;

        $this->_installDir = $installDir;
        $this->_installEtcDir = $installDir . '/app/etc';

        $customDirs = [
            DirectoryList::CONFIG => [DirectoryList::PATH => $this->_installEtcDir],
            DirectoryList::VAR_DIR => [DirectoryList::PATH => $installDir . '/var'],
            DirectoryList::MEDIA => [DirectoryList::PATH => $installDir . '/pub/media'],
            DirectoryList::STATIC_VIEW => [DirectoryList::PATH => $installDir . '/pub/static'],
            DirectoryList::GENERATION => [DirectoryList::PATH => $installDir . '/var/generation'],
            DirectoryList::CACHE => [DirectoryList::PATH => $installDir . '/var/cache'],
            DirectoryList::SESSION => [DirectoryList::PATH => $installDir . '/var/session'],
            DirectoryList::LOG => [DirectoryList::PATH => $installDir . '/var/log'],
            DirectoryList::THEMES => [DirectoryList::PATH => $installDir . '/app/design'],
        ];
        
        $this->_initParams = [
            \Magento\Framework\App\Bootstrap::INIT_PARAM_FILESYSTEM_DIR_PATHS => $customDirs,
            \Magento\Framework\App\State::PARAM_MODE => $appMode,
        ];
        $dirList = new \Magento\Framework\App\Filesystem\DirectoryList(BP, $customDirs);
        $driverPool = new \Magento\Framework\Filesystem\DriverPool;
        $this->_factory = new \Magento\TestFramework\ObjectManagerFactory($dirList, $driverPool);
    }

    /**
     * Uninstall magento
     */
    public function cleanup()
    {
        $uninstall = sprintf('php -f %s --', escapeshellarg(realpath($this->_installDir . '/dev/shell/uninstall.php')));
        passthru($uninstall, $exitCode);
        if ($exitCode) {
            exit($exitCode);
        }
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install($adminUserName, $adminPassword, $adminRoleName)
    {
        $installConfigFile = realpath(__DIR__ . '/../../../config/install.php');
        $installConfigFile = file_exists($installConfigFile) ? $installConfigFile : '{$installConfigFile}.dist';
        $installConfig = require $installConfigFile;
        $installOptions = isset($installConfig['install_options']) ? $installConfig['install_options'] : [];

        $install = sprintf('php -f %s --', escapeshellarg(realpath($this->_installDir . '/dev/shell/install.php')));
        foreach ($installOptions as $optionName => $optionValue) {
            $install .= sprintf(' --%s %s', $optionName, escapeshellarg($optionValue));
        }
        passthru($install, $exitCode);
        if ($exitCode) {
            exit($exitCode);
        }

        $this->initialize();
    }
}
