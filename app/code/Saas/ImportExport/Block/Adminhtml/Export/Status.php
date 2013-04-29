<?php
/**
 * ExportImport Status Block
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Block_Adminhtml_Export_Status extends Mage_Backend_Block_Template
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
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->_exportHelper->isTaskAdded()) {
            return parent::_toHtml();
        }
        return '';
    }
}
