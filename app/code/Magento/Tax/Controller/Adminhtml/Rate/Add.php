<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Controller\Adminhtml\Rate;

use Magento\Tax\Controller\RegistryConstants;

class Add extends \Magento\Tax\Controller\Adminhtml\Rate
{
    /**
     * Show Add Form
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Tax Zones and Rates'));

        $this->_title->add(__('New Tax Rate'));

        $this->_coreRegistry->register(
            RegistryConstants::CURRENT_TAX_RATE_FORM_DATA,
            $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true)
        );

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
