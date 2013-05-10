<?php
/**
 * Saas Export Helper
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Helper_Export extends Mage_Core_Helper_Abstract
{
    /**
     * @var Saas_ImportExport_Model_Flag
     */
    protected $_flag;

    /**
     * @var Saas_ImportExport_Model_Export
     */
    protected $_exportModel;

    /**
     * @var bool|Varien_Object
     */
    protected $_file = null;

    /**
     * @param Mage_Core_Helper_Context $context
     * @param Saas_ImportExport_Model_FlagFactory $flagFactory
     * @param Saas_ImportExport_Model_Export $exportModel
     */
    public function __construct(
        Mage_Core_Helper_Context $context,
        Saas_ImportExport_Model_FlagFactory $flagFactory,
        Saas_ImportExport_Model_Export $exportModel
    ) {
        $this->_flag = $flagFactory->create();
        $this->_flag->loadSelf();
        $this->_exportModel = $exportModel;
        parent::__construct($context);
    }

    /**
     * Is task added
     *
     * @return bool
     */
    public function isTaskAdded()
    {
        return $this->_flag->isTaskAdded();
    }

    /**
     * Get task status message
     *
     * @return string
     */
    public function getTaskStatusMessage()
    {
        $flagData = $this->_flag->getFlagData();
        return $flagData && isset($flagData['message']) ? $flagData['message'] : '';
    }

    /**
     * Is task is finished
     *
     * @return bool
     */
    public function isTaskFinished()
    {
        return $this->_flag->isTaskFinished();
    }

    /**
     * Is task in progress
     *
     * @return bool
     */
    public function isTaskProcessing()
    {
        return $this->_flag->isTaskProcessing();
    }

    /**
     * Is task notified
     *
     * @return bool
     */
    public function isTaskNotified()
    {
        return $this->_flag->isTaskNotified();
    }

    /**
     * Mark export task as queued
     *
     * @return Saas_ImportExport_Helper_Export
     */
    public function setTaskAsQueued()
    {
        $this->_flag->saveAsQueued();
        return $this;
    }

    /**
     * Remove task info
     *
     * @return Saas_ImportExport_Helper_Export
     */
    public function removeTask()
    {
        $this->_flag->delete();
        return $this;
    }

    /**
     * Checks whether export process max lifetime time reached or not
     *
     * @return bool
     */
    public function isProcessMaxLifetimeReached()
    {
        return $this->_flag->isMaxLifetimeReached();
    }

    /**
     * Get export file name for download
     *
     * @return string
     */
    public function getFileDownloadName()
    {
        return $this->isFileExist() ? $this->_getFile()->getDownloadName() : '';
    }

    /**
     * Get absolute path for export file
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->isFileExist() ? $this->_getFile()->getPath() : '';
    }

    /**
     * Is export file exist
     *
     * @return bool
     */
    public function isFileExist()
    {
        return (bool)$this->_getFile();
    }

    /**
     * Retrieve last export file information or false if not exists
     *
     * @return bool|Varien_Object
     */
    protected function _getFile()
    {
        if (is_null($this->_file)) {
            $this->_file = $this->_exportModel->getLastExportInfo();
        }
        return $this->_file;
    }
}
