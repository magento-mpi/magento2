<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Controller\Adminhtml\Rate;

class Add extends \Magento\Tax\Controller\Adminhtml\Rate
{
    /**
     * Show Add Form
     *
     * @return void
     */
    public function execute()
    {
        $rateModel = $this->_objectManager->get('Magento\Tax\Model\Calculation\Rate')->load(null);

        $this->_title->add(__('Tax Zones and Rates'));

        $this->_title->add(__('New Tax Rate'));

        $rateModel->setData($this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true));

        if ($rateModel->getZipIsRange() && !$rateModel->hasTaxPostcode()) {
            $rateModel->setTaxPostcode($rateModel->getZipFrom() . '-' . $rateModel->getZipTo());
        }

        $this->_initAction()->_addBreadcrumb(
            __('Manage Tax Rates'),
            __('Manage Tax Rates'),
            $this->getUrl('tax/rate')
        )->_addBreadcrumb(
            __('New Tax Rate'),
            __('New Tax Rate')
        )->_addContent(
            $this->_view->getLayout()->createBlock(
                'Magento\Tax\Block\Adminhtml\Rate\Toolbar\Save'
            )->assign(
                'header',
                __('Add New Tax Rate')
            )->assign(
                'form',
                $this->_view->getLayout()->createBlock('Magento\Tax\Block\Adminhtml\Rate\Form', 'tax_rate_form')
            )
        );
        $this->_view->renderLayout();
    }
}
