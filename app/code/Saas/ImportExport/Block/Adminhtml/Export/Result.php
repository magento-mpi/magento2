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
     * Get last exported file name
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->_exportHelper->getFileName();
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->isExportInProgress() || $this->getFileName()) {
            return parent::_toHtml();
        }
        return '';
    }
}
