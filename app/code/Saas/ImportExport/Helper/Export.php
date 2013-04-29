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
     * @return string
     */
    public function getFileName()
    {
        return $this->_getFile() ? $this->_getFile()->getDownloadName() : '';
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->_getFile() ? $this->_getFile()->getPath() : '';
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
