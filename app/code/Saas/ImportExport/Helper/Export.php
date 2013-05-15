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
     * @var Saas_ImportExport_Helper_Export_Config
     */
    protected $_configHelper;

    /**
     * @var bool|Varien_Object
     */
    protected $_file = null;

    /**
     * @param Mage_Core_Helper_Context $context
     * @param Saas_ImportExport_Model_FlagFactory $flagFactory
     * @param Saas_ImportExport_Helper_Export_Config $configHelper
     */
    public function __construct(
        Mage_Core_Helper_Context $context,
        Saas_ImportExport_Model_FlagFactory $flagFactory,
        Saas_ImportExport_Helper_Export_Config $configHelper
    ) {
        $this->_flag = $flagFactory->create();
        $this->_flag->loadSelf();
        $this->_configHelper = $configHelper;
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
     * Mark export task as notified
     *
     * @return Saas_ImportExport_Helper_Export
     */
    public function setTaskAsNotified()
    {
        $this->_flag->saveAsNotified();
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
     * Get export file mime type
     *
     * @return string
     */
    public function getFileMimeType()
    {
        return $this->_configHelper->getMimeTypeByExtension(
            $this->isFileExist() ? $this->_getFile()->getExtension() : ''
        );
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
     * Remove last export file
     *
     * @return Saas_ImportExport_Helper_Export
     */
    public function removeLastExportFile()
    {
        $exportFile = $this->_flag->getExportFilename();
        if ($exportFile && file_exists($exportFile) && !unlink($exportFile)) {
            Mage::throwException($this->__('File has not been removed'));
        }
        $this->removeTask();
        return $this;
    }

    /**
     * Retrieve last export file information or false if not exists
     *
     * @return bool|Varien_Object
     */
    protected function _getFile()
    {
        if (is_null($this->_file)) {
            $exportFile = $this->_flag->getExportFilename();
            if (!$exportFile || !file_exists($exportFile)) {
                return false;
            }
            $fileInfo = pathinfo($exportFile);
            $dateSuffix = date('_Ymd_His', filemtime($exportFile));
            $extension = $fileInfo['extension'];
            $this->_file = new Varien_Object(array(
                'path' => $exportFile,
                'download_name' => $fileInfo['filename'] . $dateSuffix . '.' . $extension,
                'size' => filesize($exportFile),
                'extension' => $extension
            ));
        }
        return $this->_file;
    }
}
