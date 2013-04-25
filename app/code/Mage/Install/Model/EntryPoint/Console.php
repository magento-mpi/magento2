<?php
/**
 * Console entry point
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Install_Model_EntryPoint_Console extends Mage_Core_Model_EntryPointAbstract
{
    /**
     * @param string $baseDir
     * @param array $params
     */
    public function __construct($baseDir, array $params = array())
    {
        $this->_params = $this->_buildInitParams($params);
        $config = new Mage_Core_Model_Config_Primary($baseDir, $this->_params);
        parent::__construct($config);
    }

    /**
     * Customize application init parameters
     *
     * @param array $args
     * @return array
     */
    protected function _buildInitParams(array $args)
    {
        if (!empty($args[Mage_Install_Model_Installer_Console::OPTION_URIS])) {
            $args[MAGE::PARAM_APP_URIS] =
                unserialize(base64_decode($args[Mage_Install_Model_Installer_Console::OPTION_URIS]));
        }
        if (!empty($args[Mage_Install_Model_Installer_Console::OPTION_DIRS])) {
            $args[Mage::PARAM_APP_DIRS] =
                unserialize(base64_decode($args[Mage_Install_Model_Installer_Console::OPTION_DIRS]));
        }
        return $args;
    }

    /**
     * Run http application
     */
    protected function _processRequest()
    {
        /**
         * @var $installer Mage_Install_Model_Installer_Console
         */
        $installer = $this->_objectManager->create(
            'Mage_Install_Model_Installer_Console',
            array('installArgs' => $this->_params)
        );
        if (isset($this->_params['show_locales'])) {
            var_export($installer->getAvailableLocales());
        } else if (isset($this->_params['show_currencies'])) {
            var_export($installer->getAvailableCurrencies());
        } else if (isset($this->_params['show_timezones'])) {
            var_export($installer->getAvailableTimezones());
        } else if (isset($this->_params['show_install_options'])) {
            var_export($installer->getAvailableInstallOptions());
        } else {
            $this->_handleInstall($installer);
        }
    }

    /**
     * Install/Uninstall application
     *
     * @param Mage_Install_Model_Installer_Console $installer
     *
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    protected function _handleInstall(Mage_Install_Model_Installer_Console $installer)
    {
        if (isset($this->_params['config']) && file_exists($this->_params['config'])) {
            $config = (array) include($this->_params['config']);
            $this->_params = array_merge((array)$config, $this->_params);
        }
        $isUninstallMode = isset($this->_params['uninstall']);
        if ($isUninstallMode) {
            $result = $installer->uninstall();
        } else {
            $result = $installer->install($this->_params);
        }
        if (!$installer->hasErrors()) {
            if ($isUninstallMode) {
                $msg = $result ?
                    'Uninstalled successfully' :
                    'Ignoring attempt to uninstall non-installed application';
            } else {
                $msg = 'Installed successfully' . ($result ? ' (encryption key "' . $result . '")' : '');
            }
            echo $msg . PHP_EOL;
        } else {
            echo implode(PHP_EOL, $installer->getErrors()) . PHP_EOL;
            exit(1);
        }
    }
}
