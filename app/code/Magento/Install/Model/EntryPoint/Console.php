<?php
/**
 * Console entry point
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Install_Model_EntryPoint_Console extends Magento_Core_Model_EntryPointAbstract
{
    /**
     * Application params
     *
     * @var array
     */
    protected $_params = array();

    /**
     * @param Magento_Core_Model_Config_Primary $baseDir
     * @param array $params
     * @param Magento_Core_Model_Config_Primary $config
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Install_Model_EntryPoint_Output $output
     */
    public function __construct(
        $baseDir,
        array $params = array(),
        Magento_Core_Model_Config_Primary $config = null,
        Magento_ObjectManager $objectManager = null,
        Magento_Install_Model_EntryPoint_Output $output = null
    ) {
        $this->_params = $this->_buildInitParams($params);
        if (!$config) {
            $config = new Magento_Core_Model_Config_Primary($baseDir, $this->_params);
        }
        $this->_output = $output ?: new Magento_Install_Model_EntryPoint_Output();
        parent::__construct($config, $objectManager);
    }

    /**
     * Customize application init parameters
     *
     * @param array $args
     * @return array
     */
    protected function _buildInitParams(array $args)
    {
        if (!empty($args[Magento_Install_Model_Installer_Console::OPTION_URIS])) {
            $args[MAGE::PARAM_APP_URIS] =
                unserialize(base64_decode($args[Magento_Install_Model_Installer_Console::OPTION_URIS]));
        }
        if (!empty($args[Magento_Install_Model_Installer_Console::OPTION_DIRS])) {
            $args[Mage::PARAM_APP_DIRS] =
                unserialize(base64_decode($args[Magento_Install_Model_Installer_Console::OPTION_DIRS]));
        }
        return $args;
    }

    /**
     * Run http application
     */
    protected function _processRequest()
    {
        /**
         * @var $installer Magento_Install_Model_Installer_Console
         */
        $installer = $this->_objectManager->create(
            'Magento_Install_Model_Installer_Console',
            array('installArgs' => $this->_params)
        );
        if (isset($this->_params['show_locales'])) {
            $this->_output->export($installer->getAvailableLocales());
        } else if (isset($this->_params['show_currencies'])) {
            $this->_output->export($installer->getAvailableCurrencies());
        } else if (isset($this->_params['show_timezones'])) {
            $this->_output->export($installer->getAvailableTimezones());
        } else if (isset($this->_params['show_install_options'])) {
            $this->_output->export($installer->getAvailableInstallOptions());
        } else {
            $this->_handleInstall($installer);
        }
    }

    /**
     * Install/Uninstall application
     *
     * @param Magento_Install_Model_Installer_Console $installer
     */
    protected function _handleInstall(Magento_Install_Model_Installer_Console $installer)
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
            $this->_output->success($msg . PHP_EOL);
        } else {
            $this->_output->error(implode(PHP_EOL, $installer->getErrors()) . PHP_EOL);
        }
    }
}
