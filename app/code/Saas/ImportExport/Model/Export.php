<?php
/**
 * Export model Process Manager
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Model_Export extends Varien_Object
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
     * @var array
     */
    protected $_params = array();
    /**
     * @var Saas_ImportExport_Helper_Export_Config
     */
    protected $_configHelper;

    /**
     * @var Saas_ImportExport_Model_Flag
     */
    protected $_flag;

    /**
     * @var bool
     */
    protected $_finishedFlag = false;

    /**
     * Constructor
     *
     * @param Saas_ImportExport_Model_Export_EntityFactory $entityFactory
     * @param Saas_ImportExport_Model_Export_StorageFactory $storageFactory
     * @param Saas_ImportExport_Helper_Export_Config $configHelper
     * @param Saas_ImportExport_Model_FlagFactory $flagFactory
     * @param array $data
     */
    public function __construct(
        Saas_ImportExport_Model_Export_EntityFactory $entityFactory,
        Saas_ImportExport_Model_Export_StorageFactory $storageFactory,
        Saas_ImportExport_Helper_Export_Config $configHelper,
        Saas_ImportExport_Model_FlagFactory $flagFactory,
        array $data = array()
    ) {
        parent::__construct($data);
        $this->_exportEntityFactory = $entityFactory;
        $this->_storageAdapterFactory = $storageFactory;
        $this->_configHelper = $configHelper;
        $this->_flag = $flagFactory->create();
        $this->_flag->loadSelf();
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
     * @param array $params
     * @return null
     */
    public function export($params)
    {
        $this->_initParams($params);
        $this->_paginateCollection();
        if ($this->_isCanExport()) {
            $this->_saveHeaderColumns();
            $this->_exportEntity->exportCollection();
            $this->_saveExportState();
        } else {
            $this->_setIsFinished();
        }
    }

    /**
     * Init parameters needed for export
     *
     * @param $params
     */
    protected function _initParams($params)
    {
        try {
            $this->_params = $params;
            $this->_storageAdapter = $this->_storageAdapterFactory->create(
                $this->_getStorageFormat(),
                $this->_configHelper->getStorageFilePath($this->_getEntityType())
            );
            $this->_exportEntity = $this->_exportEntityFactory->create($this->_getEntityType(), $params);
            $this->_exportEntity->setWriter($this->_storageAdapter);
        } catch (Exception $e) {
            $this->_setIsFinished();
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
        if (!$this->_exportEntity->getCollection()->getSize()) {
            return false;
        }
        return true;
    }

    /**
     * Save header columns to storage
     */
    protected function _saveHeaderColumns()
    {
        $headerCols = $this->_exportEntity->getHeaderCols();
        if ($this->_getCurrentPage() == 1) {
            $this->_flag->saveAsProcessing();
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
            $this->_setIsFinished();
            $this->_flag->saveAsFinished();
            $exportFile = $this->_storageAdapter->renameTemporaryFile();
            $this->_flag->saveExportFilename($exportFile);
        } else {
            $this->_flag->saveStatusMessage(ceil($this->_getCurrentPage() * 100 / $countPages) . '%');
        }
    }

    /**
     * Get export entity type
     *
     * @return string
     */
    protected function _getEntityType()
    {
        return isset($this->_params['entity']) ? $this->_params['entity'] : '';
    }

    /**
     * Get export storage file format
     *
     * @return string
     */
    protected function _getStorageFormat()
    {
        return isset($this->_params['file_format']) ? $this->_params['file_format'] : '';
    }

    /**
     * Get current page
     *
     * @return int
     */
    protected function _getCurrentPage()
    {
        return isset($this->_params['page']) ? $this->_params['page'] : 1;
    }

    /**
     * Set finished flag as true
     */
    protected function _setIsFinished()
    {
        $this->_finishedFlag = true;
    }
}
