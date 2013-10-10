<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Import model
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\ScheduledImportExport\Model;

class Import extends \Magento\ImportExport\Model\Import
    implements \Magento\ScheduledImportExport\Model\Scheduled\Operation\OperationInterface
{
    /**
     * Reindex indexes by process codes.
     *
     * @return \Magento\ScheduledImportExport\Model\Import
     */
    public function reindexAll()
    {
        if (!isset(self::$_entityInvalidatedIndexes[$this->getEntity()])) {
            return $this;
        }

        $indexers = self::$_entityInvalidatedIndexes[$this->getEntity()];
        foreach ($indexers as $indexer) {
            $indexProcess = $this->_indexer->getProcessByCode($indexer);
            if ($indexProcess) {
                $indexProcess->reindexEverything();
            }
        }

        return $this;
    }

    /**
     * Run import through cron
     *
     * @param \Magento\ScheduledImportExport\Model\Scheduled\Operation $operation
     * @return bool
     */
    public function runSchedule(\Magento\ScheduledImportExport\Model\Scheduled\Operation $operation)
    {
        $sourceFile = $operation->getFileSource($this);
        $result = false;
        if ($sourceFile) {
            $result = $this->validateSource(\Magento\ImportExport\Model\Import\Adapter::findAdapterFor($sourceFile));
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
     * @param \Magento\ScheduledImportExport\Model\Scheduled\Operation $operation
     * @return \Magento\ScheduledImportExport\Model\Import
     */
    public function initialize(\Magento\ScheduledImportExport\Model\Scheduled\Operation $operation)
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
