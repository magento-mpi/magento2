<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Block\Adminhtml\Billing;

/**
 * Adminhtml billing agreement grid container
 */
class Agreement extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Initialize billing agreements grid container
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_billing_agreement';
        $this->_blockGroup = 'Magento_Paypal';
        $this->_headerText = __('Billing Agreements');
        parent::_construct();
        $this->buttonList->remove('add');
    }
}
