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
class Benchmark_Application
{
    /**
     * @var string
     */
    protected $_baseDir;

    /**
     * @var Magento_Shell
     */
    protected $_shell;

    /**
     * @var string
     */
    protected $_installerScript;

    /**
     * Constructor
     *
     * @param string $baseDir
     * @param Magento_Shell $shell
     * @throws Magento_Exception
     */
    public function __construct($baseDir, Magento_Shell $shell)
    {
        $this->_baseDir = $baseDir;
        $this->_shell = $shell;
        $installerScript = $this->_baseDir . '/dev/shell/install.php';
        if (!file_exists($installerScript)) {
            throw new Magento_Exception(
                "Console installer '$installerScript' does not exist."
                . " Directory '$this->_baseDir' does not seem to be valid Magento root."
            );
        }
        $this->_installerScript = realpath($installerScript);
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
        $installCmd = 'php -f %s --';
        $installCmdArgs = array($this->_installerScript);
        foreach ($options as $optionName => $optionValue) {
            $installCmd .= " --$optionName %s";
            $installCmdArgs[] = $optionValue;
        }
        $this->_shell->execute($installCmd, $installCmdArgs);
        $this->_bootstrap();
        $this->_applyFixtures($fixtureFiles);
        $this->_reindex();
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
}
