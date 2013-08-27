<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Import model
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_ImportExport_Model_Import extends Magento_ImportExport_Model_Import
    implements Enterprise_ImportExport_Model_Scheduled_Operation_Interface
{
    /**
     * Reindex indexes by process codes.
     *
     * @return Enterprise_ImportExport_Model_Import
     */
    public function reindexAll()
    {
        if (!isset(self::$_entityInvalidatedIndexes[$this->getEntity()])) {
            return $this;
        }

        $indexers = self::$_entityInvalidatedIndexes[$this->getEntity()];
        foreach ($indexers as $indexer) {
            $indexProcess = Mage::getSingleton('Magento_Index_Model_Indexer')->getProcessByCode($indexer);
            if ($indexProcess) {
                $indexProcess->reindexEverything();
            }
        }

        return $this;
    }

    /**
     * Run import through cron
     *
     * @param Enterprise_ImportExport_Model_Scheduled_Operation $operation
     * @return bool
     */
    public function runSchedule(Enterprise_ImportExport_Model_Scheduled_Operation $operation)
    {
        $sourceFile = $operation->getFileSource($this);
        $result = false;
        if ($sourceFile) {
            $result = $this->validateSource(Magento_ImportExport_Model_Import_Adapter::findAdapterFor($sourceFile));
        }
        $isAllowedForcedImport = $operation->getForceImport()
            && $this->getProcessedRowsCount() != $this->getInvalidRowsCount();
        if ($isAllowedForcedImport || $result) {
            $result = $this->importSource();
        }
        if ($result) {
            $this->reindexAll();
        }
        return (bool)$result;
    }

    /**
     * Initialize import instance from scheduled operation
     *
     * @param Enterprise_ImportExport_Model_Scheduled_Operation $operation
     * @return Enterprise_ImportExport_Model_Import
     */
    public function initialize(Enterprise_ImportExport_Model_Scheduled_Operation $operation)
    {
        $this->setData(array(
            'entity'                 => $operation->getEntityType(),
            'behavior'               => $operation->getBehavior(),
            'operation_type'         => $operation->getOperationType(),
            'run_at'                 => $operation->getStartTime(),
            'scheduled_operation_id' => $operation->getId()
        ));
        return $this;
    }
}
