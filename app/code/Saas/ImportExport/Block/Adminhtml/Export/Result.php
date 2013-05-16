<?php
/**
 * ExportImport Status Block
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Block_Adminhtml_Export_Result extends Mage_Backend_Block_Template
{
    /**
     * How often should be status of export checked
     */
    const TIMEOUT_CHECK_EXPORT_PROGRESS = 3;

    /**
     * @var Saas_ImportExport_Helper_Export_State
     */
    protected $_stateHelper;

    /**
     * @var Saas_ImportExport_Helper_Export_File
     */
    protected $_fileHelper;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Saas_ImportExport_Helper_Export_State $stateHelper
     * @param Saas_ImportExport_Helper_Export_File $fileHelper,
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Saas_ImportExport_Helper_Export_State $stateHelper,
        Saas_ImportExport_Helper_Export_File $fileHelper,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_stateHelper = $stateHelper;
        $this->_fileHelper = $fileHelper;
    }

    /**
     * @return bool
     */
    public function isExportInProgress()
    {
        return $this->_stateHelper->isInProgress();
    }

    /**
     * Get url for download export file
     *
     * @return string
     */
    public function getCheckExportUrl()
    {
        return $this->getUrl('*/*/check');
    }

    /**
     * Returns how often should be status of export checked in ms
     *
     * @return int
     */
    public function getCheckExportTimeout()
    {
        return self::TIMEOUT_CHECK_EXPORT_PROGRESS * 1000;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->isExportInProgress() || $this->_fileHelper->isExist()) {
            return parent::_toHtml();
        }
        return '';
    }
}
