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
class Magento_ScheduledImportExport_Model_Import extends Magento_ImportExport_Model_Import
    implements Magento_ScheduledImportExport_Model_Scheduled_Operation_Interface
{
    /**
     * @var Magento_Index_Model_Indexer
     */
    protected $_indexer;

    /**
     * @param Magento_Index_Model_Indexer $indexer
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_ImportExport_Helper_Data $importExportData
     * @param Magento_ImportExport_Model_Import_ConfigInterface $importConfig
     * @param array $data
     */
    public function __construct(
        Magento_Index_Model_Indexer $indexer,
        Magento_Core_Model_Logger $logger,
        Magento_ImportExport_Helper_Data $importExportData,
        Magento_ImportExport_Model_Import_ConfigInterface $importConfig,
        array $data = array()
    ) {
        $this->_indexer = $indexer;
        parent::__construct($logger, $importExportData, $importConfig, $data);
    }

    /**
     * Reindex indexes by process codes.
     *
     * @return Magento_ScheduledImportExport_Model_Import
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
     * @param Magento_ScheduledImportExport_Model_Scheduled_Operation $operation
     * @return bool
     */
    public function runSchedule(Magento_ScheduledImportExport_Model_Scheduled_Operation $operation)
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
     * @param Magento_ScheduledImportExport_Model_Scheduled_Operation $operation
     * @return Magento_ScheduledImportExport_Model_Import
     */
    public function initialize(Magento_ScheduledImportExport_Model_Scheduled_Operation $operation)
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
