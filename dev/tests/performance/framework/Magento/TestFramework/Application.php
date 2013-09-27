<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     performance_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Magento application for performance tests
 */
class Magento_TestFramework_Application
{
    /**
     * Configuration object
     *
     * @param Magento_Config
     */
    protected $_config;

    /**
     * Path to shell installer script
     *
     * @var string
     */
    protected $_installerScript;

    /**
     * @var Magento_Shell
     */
    protected $_shell;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Whether application is installed
     *
     * @var bool
     */
    protected $_isInstalled = false;

    /**
     * List of fixtures applied to the application
     *
     * @var array
     */
    protected $_fixtures = array();

    /**
     * Constructor
     *
     * @param Magento_TestFramework_Performance_Config $config
     * @param Magento_Shell $shell
     * @throws Magento_Exception
     */
    public function __construct(Magento_TestFramework_Performance_Config $config, Magento_Shell $shell)
    {
        $installerScript = $config->getApplicationBaseDir() . '/dev/shell/install.php';
        if (!is_file($installerScript)) {
            throw new Magento_Exception("File '$installerScript' is not found.");
        }
        $this->_installerScript = realpath($installerScript);
        $this->_config = $config;
        $this->_shell = $shell;
    }

    /**
     * Reset application - i.e. cleanup already installed app, or install it otherwise
     *
     * @return Magento_TestFramework_Application
     */
    protected function _reset()
    {
        if ($this->_config->getInstallOptions()) {
            $this->_uninstall()
                ->_install()
                ->_reindex()
                ->_updateFilesystemPermissions();
        } else {
            $this->_isInstalled = true;
        }
        return $this;
    }

    /**
     * Uninstall application
     *
     * @return Magento_TestFramework_Application
     */
    protected function _uninstall()
    {
        $this->_shell->execute('php -f %s -- --uninstall', array($this->_installerScript));

        $this->_isInstalled = false;
        $this->_fixtures = array();

        return $this;
    }

    /**
     * Install application according to installation options
     *
     * @return Magento_TestFramework_Application
     * @throws Magento_Exception
     */
    protected function _install()
    {
        $installOptions = $this->_config->getInstallOptions();
        if (!$installOptions) {
            throw new Magento_Exception('Trying to install Magento, but installation options are not set');
        }

        // Populate install options with global options
        $baseUrl = 'http://' . $this->_config->getApplicationUrlHost() . $this->_config->getApplicationUrlPath();
        $installOptions = array_merge($installOptions, array('url' => $baseUrl, 'secure_base_url' => $baseUrl));
        $adminOptions = $this->_config->getAdminOptions();
        foreach ($adminOptions as $key => $val) {
            $installOptions['admin_' . $key] = $val;
        }

        $installCmd = 'php -f %s --';
        $installCmdArgs = array($this->_installerScript);
        foreach ($installOptions as $optionName => $optionValue) {
            $installCmd .= " --$optionName %s";
            $installCmdArgs[] = $optionValue;
        }
        $this->_shell->execute($installCmd, $installCmdArgs);

        $this->_isInstalled = true;
        $this->_fixtures = array();
        return $this;
    }

    /**
     * Run all indexer processes
     *
     * @return Magento_TestFramework_Application
     */
    protected function _reindex()
    {
        $this->_bootstrap();

        /** @var $indexer Magento_Index_Model_Indexer */
        $indexer = Mage::getModel('Magento_Index_Model_Indexer');
        /** @var $process Magento_Index_Model_Process */
        foreach ($indexer->getProcessesCollection() as $process) {
            if ($process->getIndexer()->isVisible()) {
                $process->reindexEverything();
            }
        }

        return $this;
    }

    /**
     * Update permissions for `var` directory
     */
    protected function _updateFilesystemPermissions()
    {
        Magento_Io_File::chmodRecursive(Mage::getBaseDir('var'), 0777);
    }

    /**
     * Bootstrap application, so it is possible to use its resources
     *
     * @return Magento_TestFramework_Application
     */
    protected function _bootstrap()
    {
        if (!$this->_objectManager) {
            $this->_objectManager = new Magento_Core_Model_ObjectManager(
                new Magento_Core_Model_Config_Primary(BP, $_SERVER)
            );
        }
        /** @var $app Magento_Core_Model_App */
        $this->_objectManager->get('Magento_Core_Model_App');
        return $this;
    }

    /**
     * Work on application, so that it has all and only $fixtures applied. May require reinstall, if
     * excessive fixtures has been applied before.
     *
     * @param array $fixtures
     */
    public function applyFixtures(array $fixtures)
    {
        if (!$this->_isInstalled || $this->_doFixturesNeedReinstall($fixtures)) {
            $this->_reset();
        }

        // Apply fixtures
        $fixturesToApply = array_diff($fixtures, $this->_fixtures);
        if (!$fixturesToApply) {
            return;
        }

        $this->_bootstrap();
        foreach ($fixturesToApply as $fixtureFile) {
            require $fixtureFile;
        }
        $this->_fixtures = $fixtures;

        $this->_reindex()
            ->_updateFilesystemPermissions();
    }

    /**
     * Compare list of fixtures needed to be set to the application, with the list of fixtures already in it.
     * Return, whether application reinstall (cleanup) is needed to properly apply the fixtures.
     *
     * @param array $fixtures
     * @return bool
     */
    protected function _doFixturesNeedReinstall($fixtures)
    {
        $excessiveFixtures = array_diff($this->_fixtures, $fixtures);
        return (bool)$excessiveFixtures;
    }
}
