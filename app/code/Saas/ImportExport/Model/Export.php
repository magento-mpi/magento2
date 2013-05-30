<?php
/**
 * Export model Process Manager
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Model_Export
{
    /**
     * @var Saas_ImportExport_Model_Export_EntityInterface
     */
    protected $_exportEntity;

    /**
     * @var Saas_ImportExport_Model_Export_Adapter_AdapterAbstract
     */
    protected $_storageAdapter;

    /**
     * @var Saas_ImportExport_Model_Export_EntityFactory
     */
    protected $_exportEntityFactory;

    /**
     * @var Saas_ImportExport_Model_Export_StorageFactory
     */
    protected $_storageFactory;

    /**
     * @var Saas_ImportExport_Helper_Export_Config
     */
    protected $_configHelper;

    /**
     * @var Saas_ImportExport_Helper_Export_State
     */
    protected $_stateHelper;

    /**
     * @var bool
     */
    protected $_finishedFlag = false;

    /**
     * @var array
     */
    protected $_options = array();

    /**
     * @var Mage_Core_Model_Logger
     */
    protected $_logger;

    /**
     * Constructor
     *
     * @param Saas_ImportExport_Model_Export_EntityFactory $entityFactory
     * @param Saas_ImportExport_Model_Export_StorageFactory $storageFactory
     * @param Saas_ImportExport_Helper_Export_Config $configHelper
     * @param Saas_ImportExport_Helper_Export_State $stateHelper
     * @param Mage_Core_Model_Logger $logger
     */
    public function __construct(
        Saas_ImportExport_Model_Export_EntityFactory $entityFactory,
        Saas_ImportExport_Model_Export_StorageFactory $storageFactory,
        Saas_ImportExport_Helper_Export_Config $configHelper,
        Saas_ImportExport_Helper_Export_State $stateHelper,
        Mage_Core_Model_Logger $logger
    ) {
        $this->_exportEntityFactory = $entityFactory;
        $this->_storageFactory = $storageFactory;
        $this->_configHelper = $configHelper;
        $this->_stateHelper = $stateHelper;
        $this->_logger = $logger;
    }

    /**
     * Is export process totally finished
     *
     * @return bool
     */
    public function isFinished()
    {
        return $this->_finishedFlag;
    }

    /**
     * Export process
     *
     * @param array $options
     */
    public function export($options)
    {
        try {
            $this->_init($options);
            $this->_paginateCollection();
            $this->_saveHeaderColumns();
            if ($this->_isCanExport()) {
                $this->_export();
            } else {
                $this->_finishExportSuccess();
            }
        } catch (Exception $e) {
            $this->_finishExportFail($e);
        }
    }

    /**
     * Init parameters needed for export
     *
     * @param array $options
     * @throws Exception
     */
    protected function _init($options)
    {
        try {
            $this->_options = $options;
            $this->_storageAdapter = $this->_storageFactory->create(
                $this->_getStorageFormat(),
                $this->_configHelper->getStorageFilePath($this->_getEntityType())
            );
            $this->_exportEntity = $this->_exportEntityFactory->create($this->_getEntityType(), $options);
            $this->_exportEntity->setStorageAdapter($this->_storageAdapter);
            $this->_exportEntity->prepareCollection();
            if ($this->_getCurrentPage() == 1) {
                $this->_storageAdapter->cleanupWorkingDir();
                $this->_stateHelper->saveTaskAsProcessing();
            }
        } catch (Exception $e) {
            $this->_logger->logException($e);
            $this->_finishExportFail($e);
            throw new Exception('Cannot init export process');
        }
    }

    /**
     * Limit collection
     */
    protected function _paginateCollection()
    {
        $this->_exportEntity
            ->getCollection()
            ->setCurPage($this->_getCurrentPage())
            ->setPageSize($this->_configHelper->getItemsPerPage($this->_getEntityType()));
    }

    /**
     * Is can start export
     *
     * @return bool
     */
    protected function _isCanExport()
    {
        return (bool)$this->_exportEntity->getCollection()->getSize();
    }

    /**
     * Start exporting
     */
    protected function _export()
    {
        try {
            $this->_exportEntity->exportCollection();
            if ($this->_isExportFinished()) {
                $this->_finishExportSuccess();
            } else {
                $this->_saveTaskStatusMessage();
            }
        } catch (Exception $e) {
            $this->_finishExportFail($e);
        }
    }

    /**
     * Save header columns to storage
     */
    protected function _saveHeaderColumns()
    {
        $headerCols = $this->_exportEntity->getHeaderColumns();
        if ($this->_getCurrentPage() == 1) {
            $this->_storageAdapter->writeHeaderColumns($headerCols);
        } else {
            $this->_storageAdapter->saveHeaderColumns($headerCols);
        }
    }

    /**
     * Is export totally finished
     *
     * @return bool
     */
    protected function _isExportFinished()
    {
        return $this->_getCurrentPage() >= $this->_exportEntity->getCollection()->getLastPageNumber();
    }

    /**
     * Save task status message
     */
    protected function _saveTaskStatusMessage()
    {
        $this->_stateHelper->saveTaskStatusMessage(ceil($this->_getCurrentPage() * 100 /
            $this->_exportEntity->getCollection()->getLastPageNumber()) . '%');
    }

    /**
     * Save export as finished
     */
    protected function _saveAsFinished()
    {
        $this->_finishedFlag = true;
        $this->_stateHelper->saveTaskAsFinished();
    }

    /**
     * Success finish export, save output file
     */
    protected function _finishExportSuccess()
    {
        $this->_saveAsFinished();
        try {
            $this->_stateHelper->saveExportFilename($this->_storageAdapter->renameTemporaryFile());
        } catch (Magento_Filesystem_Exception $e) {
            $this->_logger->logException($e);
        }
    }

    /**
     * Fail finish export
     *
     * @param Exception $exception
     */
    protected function _finishExportFail(Exception $exception)
    {
        $this->_saveAsFinished();
        $this->_logger->logException($exception);
    }

    /**
     * Get export entity type
     *
     * @return string
     */
    protected function _getEntityType()
    {
        return isset($this->_options['entity']) ? $this->_options['entity'] : '';
    }

    /**
     * Get export storage file format
     *
     * @return string
     */
    protected function _getStorageFormat()
    {
        return isset($this->_options['file_format']) ? $this->_options['file_format'] : '';
    }

    /**
     * Get current page
     *
     * @return int
     */
    protected function _getCurrentPage()
    {
        return isset($this->_options['page']) ? $this->_options['page'] : 1;
    }
}
