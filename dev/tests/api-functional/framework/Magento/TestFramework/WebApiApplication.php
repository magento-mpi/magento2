<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestFramework;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Provides access to the application for the tests
 *
 * Allows installation and uninstallation
 */
class WebApiApplication extends Application
{
    /**
     * @param string $installConfigFile
     * @param string $globalConfigDir
     * @param array $moduleConfigFiles
     * @param string $appMode
     * @param string $tmpDir
     * @param \Magento\Framework\Shell $shell
     * @return Application|WebApiApplication
     */
    public static function getInstance(
        $installConfigFile,
        $globalConfigDir,
        array $moduleConfigFiles,
        $appMode,
        $tmpDir,
        \Magento\Framework\Shell $shell
    ) {
        if (!file_exists($installConfigFile)) {
            $installConfigFile = $installConfigFile . '.dist';
        }
        $dirList = new \Magento\Framework\App\Filesystem\DirectoryList(BP);
        return new \Magento\TestFramework\WebApiApplication(
            $shell,
            $dirList->getPath(DirectoryList::VAR_DIR),
            $installConfigFile,
            $globalConfigDir,
            $moduleConfigFiles,
            $appMode
        );
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        throw new \Exception(
            "Can't start application: purpose of Web API Application is to use classes and models from the application"
            . " and don't run it"
        );
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        $installOptions = $this->getInstallConfig();

        /* Install application */
        if ($installOptions) {
            $installCmd = 'php -f ' . BP . '/setup/index.php install';
            $installArgs = [];
            foreach ($installOptions as $optionName => $optionValue) {
                if (is_bool($optionValue)) {
                    if (true === $optionValue) {
                        $installCmd .= " --$optionName";
                    }
                    continue;
                }
                $installCmd .= " --$optionName=%s";
                $installArgs[] = $optionValue;
            }
            $this->_shell->execute($installCmd, $installArgs);
        }
    }

    /**
     * Use the application as is
     *
     * {@inheritdoc}
     */
    protected function getCustomDirs()
    {
        return [];
    }
}
