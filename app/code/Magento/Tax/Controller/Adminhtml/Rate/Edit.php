<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Controller\Adminhtml\Rate;

class Edit extends \Magento\Tax\Controller\Adminhtml\Rate
{
    /**
     * Show Edit Form
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Tax Zones and Rates'));

        $rateId = (int)$this->getRequest()->getParam('rate');
        $rateModel = $this->_objectManager->get('Magento\Tax\Model\Calculation\Rate')->load($rateId);
        if (!$rateModel->getId()) {
            $this->getResponse()->setRedirect($this->getUrl("*/*/"));
            return;
        }

        if ($rateModel->getZipIsRange() && !$rateModel->hasTaxPostcode()) {
            $rateModel->setTaxPostcode($rateModel->getZipFrom() . '-' . $rateModel->getZipTo());
        }

        $this->_title->add(sprintf("%s", $rateModel->getCode()));

        $this->_initAction()->_addBreadcrumb(
            __('Manage Tax Rates'),
            __('Manage Tax Rates'),
            $this->getUrl('tax/rate')
        )->_addBreadcrumb(
            __('Edit Tax Rate'),
            __('Edit Tax Rate')
        )->_addContent(
            $this->_view->getLayout()->createBlock(
                'Magento\Tax\Block\Adminhtml\Rate\Toolbar\Save'
            )->assign(
                'header',
                __('Edit Tax Rate')
            )->assign(
                'form',
                $this->_view->getLayout()->createBlock(
                    'Magento\Tax\Block\Adminhtml\Rate\Form',
                    'tax_rate_form'
                )->setShowLegend(
                    true
                )
            )
        );
        $this->_view->renderLayout();
    }
}
