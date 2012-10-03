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
class Magento_Installer
{
    /**
     * @var string
     */
    protected $_installerScript;

    /**
     * @var Magento_Shell
     */
    protected $_shell;

    /**
     * Constructor
     *
     * @param string $installerScript
     * @param Magento_Shell $shell
     * @throws Magento_Exception
     */
    public function __construct($installerScript, Magento_Shell $shell)
    {
        if (!is_file($installerScript)) {
            throw new Magento_Exception("File '$installerScript' is not found.");
        }

        $this->_installerScript = realpath($installerScript);
        $this->_shell = $shell;
    }

    /**
     * Uninstall application
     */
    public function uninstall()
    {
        $this->_shell->execute('php -f %s -- --uninstall', array($this->_installerScript));
    }

    /**
     * Install application according to installation options and apply fixtures
     *
     * @param array $options
     * @param array $fixtureFiles
     */
    public function install(array $options, array $fixtureFiles = array())
    {
        $this->_install($options);
        $this->_bootstrap();
        $this->_applyFixtures($fixtureFiles);
        $this->_reindex();
        $this->_updateFilesystemPermissions();
    }

    /**
     * Perform installation of Magento app
     *
     * @param array $options
     */
    protected function _install($options)
    {
        $installCmd = 'php -f %s --';
        $installCmdArgs = array($this->_installerScript);
        foreach ($options as $optionName => $optionValue) {
            $installCmd .= " --$optionName %s";
            $installCmdArgs[] = $optionValue;
        }
        $this->_shell->execute($installCmd, $installCmdArgs);
    }

    /**
     * Bootstrap installed application
     */
    protected function _bootstrap()
    {
        Mage::app();
    }

    /**
     * Apply fixture scripts
     *
     * @param array $fixtureFiles
     */
    protected function _applyFixtures(array $fixtureFiles)
    {
        foreach ($fixtureFiles as $oneFixtureFile) {
            require $oneFixtureFile;
        }
    }

    /**
     * Run all indexer processes
     */
    protected function _reindex()
    {
        /** @var $indexer Mage_Index_Model_Indexer */
        $indexer = Mage::getModel('Mage_Index_Model_Indexer');
        /** @var $process Mage_Index_Model_Process */
        foreach ($indexer->getProcessesCollection() as $process) {
            if ($process->getIndexer()->isVisible()) {
                $process->reindexEverything();
            }
        }
    }

    /**
     * Update permissions for `var` directory
     */
    protected function _updateFilesystemPermissions()
    {
        Varien_Io_File::chmodRecursive(Mage::getBaseDir('var'), 0777);
    }
}
