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
     * @var Saas_ImportExport_Helper_Export
     */
    protected $_exportHelper;
    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Saas_ImportExport_Helper_Export $exportHelper
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Saas_ImportExport_Helper_Export $exportHelper,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_exportHelper = $exportHelper;
    }

    /**
     * @return bool
     */
    public function isExportInProgress()
    {
        return $this->_exportHelper->isTaskAdded();
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
        if ($this->isExportInProgress() || $this->_exportHelper->isFileExist()) {
            return parent::_toHtml();
        }
        return '';
    }
}
