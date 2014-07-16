<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Controller\Adminhtml\Rate;

class ImportExport extends \Magento\Tax\Controller\Adminhtml\Rate
{
    /**
     * Import and export Page
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Tax Zones and Rates'));

        $this->_title->add(__('Import and Export Tax Rates'));

        $this->_view->loadLayout();
        $this->_setActiveMenu(
            'Magento_Tax::system_convert_tax'
        )->_addContent(
            $this->_view->getLayout()->createBlock('Magento\Tax\Block\Adminhtml\Rate\ImportExportHeader')
        )->_addContent(
            $this->_view->getLayout()->createBlock('Magento\Tax\Block\Adminhtml\Rate\ImportExport')
        );
        $this->_view->renderLayout();
    }
}
