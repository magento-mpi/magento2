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
     * @var Saas_ImportExport_Model_Export_Adapter_Abstract
     */
    protected $_storageAdapter;

    /**
     * @var Saas_ImportExport_Model_Export_EntityFactory
     */
    protected $_exportEntityFactory;

    /**
     * @var Saas_ImportExport_Model_Export_StorageFactory
     */
    protected $_storageAdapterFactory;

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
     * Constructor
     *
     * @param Saas_ImportExport_Model_Export_EntityFactory $entityFactory
     * @param Saas_ImportExport_Model_Export_StorageFactory $storageFactory
     * @param Saas_ImportExport_Helper_Export_Config $configHelper
     * @param Saas_ImportExport_Helper_Export_State $stateHelper
     */
    public function __construct(
        Saas_ImportExport_Model_Export_EntityFactory $entityFactory,
        Saas_ImportExport_Model_Export_StorageFactory $storageFactory,
        Saas_ImportExport_Helper_Export_Config $configHelper,
        Saas_ImportExport_Helper_Export_State $stateHelper
    ) {
        $this->_exportEntityFactory = $entityFactory;
        $this->_storageAdapterFactory = $storageFactory;
        $this->_configHelper = $configHelper;
        $this->_stateHelper = $stateHelper;
    }

    /**
     * Is export process totally finished
     *
     * @return bool
     */
    public function getIsFinished()
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
        $this->_init($options);
        $this->_paginateCollection();
        if ($this->_isCanExport()) {
            $this->_export();
        } else {
            $this->_finishExport();
        }
    }

    /**
     * Init parameters needed for export
     *
     * @param array $options
     */
    protected function _init($options)
    {
        try {
            $this->_options = $options;
            $this->_storageAdapter = $this->_storageAdapterFactory->create(
                $this->_getStorageFormat(),
                $this->_configHelper->getStorageFilePath($this->_getEntityType())
            );
            $this->_exportEntity = $this->_exportEntityFactory->create($this->_getEntityType(), $options);
            $this->_exportEntity->setStorageAdapter($this->_storageAdapter);
            $this->_exportEntity->prepareCollection();
            if ($this->_getCurrentPage() == 1) {
                $this->_storageAdapter->cleanupWorkingDir();
                $this->_stateHelper->setTaskAsProcessing();
            }
        } catch (Exception $e) {
            $this->_finishExport();
            Mage::logException($e);
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
            $this->_saveHeaderColumns();
            $this->_exportEntity->exportCollection();
            $this->_saveExportState();
        } catch (Exception $e) {
            $this->_finishExport();
            Mage::logException($e);
        }
    }

    /**
     * Save header columns to storage
     */
    protected function _saveHeaderColumns()
    {
        $headerCols = $this->_exportEntity->getHeaderColumns();
        if ($this->_getCurrentPage() == 1) {
            $this->_storageAdapter->setHeaderCols($headerCols);
        } else {
            $this->_storageAdapter->setHeaderColsData($headerCols);
        }
    }

    /**
     * Save export state
     */
    protected function _saveExportState()
    {
        $countPages = $this->_exportEntity->getCollection()->getLastPageNumber();
        if ($this->_getCurrentPage() >= $countPages) {
            $this->_finishExport(true);
        } else {
            $this->_stateHelper->saveTaskStatusMessage(ceil($this->_getCurrentPage() * 100 / $countPages) . '%');
        }
    }

    /**
     * Set finished flag as true and try save export file
     *
     * @param bool $saveFile
     */
    protected function _finishExport($saveFile = false)
    {
        $this->_finishedFlag = true;
        $this->_stateHelper->setTaskAsFinished();
        if ($saveFile && $this->_storageAdapter) {
            $exportFile = $this->_storageAdapter->renameTemporaryFile();
            $this->_stateHelper->saveExportFilename($exportFile);
        }
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
