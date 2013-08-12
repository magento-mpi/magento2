<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Index_Model_EntryPoint_Indexer extends Magento_Core_Model_EntryPointAbstract
{
    /**
     * Report directory
     *
     * @var string
     */
    protected $_reportDir;

    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * @param string $reportDir absolute path to report directory to be cleaned
     * @param Magento_Filesystem $filesystem
     * @param Magento_Core_Model_Config_Primary $config
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(
        $reportDir,
        Magento_Filesystem $filesystem,
        Magento_Core_Model_Config_Primary $config,
        Magento_ObjectManager $objectManager = null
    ) {
        parent::__construct($config, $objectManager);
        $this->_reportDir = $reportDir;
        $this->_filesystem = $filesystem;
    }

    /**
     * Process request to application
     */
    protected function _processRequest()
    {
        /* Clean reports */
        $this->_filesystem->delete($this->_reportDir, dirname($this->_reportDir));

        /* Run all indexer processes */
        /** @var $indexer Magento_Index_Model_Indexer */
        $indexer = $this->_objectManager->create('Magento_Index_Model_Indexer');
        /** @var $process Magento_Index_Model_Process */
        foreach ($indexer->getProcessesCollection() as $process) {
            if ($process->getIndexer()->isVisible()) {
                $process->reindexEverything();
            }
        }
    }
}
