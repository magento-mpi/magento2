<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Index_Model_EntryPoint_Indexer extends Mage_Core_Model_EntryPointAbstract
{
    /**
     * @var array
     */
    protected $_params;

    /**
     * @param string $baseDir
     * @param array $params
     */
    public function __construct($baseDir, array $params = array())
    {
        $this->_params = $params;
        unset($params['reportDir']);
        parent::__construct(new Mage_Core_Model_Config_Primary($baseDir, $params));
    }

    /**
     * Process request to application
     */
    protected function _processRequest()
    {
        /* Clean reports */
        Varien_Io_File::rmdirRecursive($this->_params['reportDir']);

        /* Run all indexer processes */
        /** @var $indexer Mage_Index_Model_Indexer */
        $indexer = $this->_objectManager->create('Mage_Index_Model_Indexer');
        /** @var $process Mage_Index_Model_Process */
        foreach ($indexer->getProcessesCollection() as $process) {
            if ($process->getIndexer()->isVisible()) {
                $process->reindexEverything();
            }
        }
    }
}
