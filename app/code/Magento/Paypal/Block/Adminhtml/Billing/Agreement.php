<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml billing agreement grid container
 */
namespace Magento\Paypal\Block\Adminhtml\Billing;

class Agreement extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Initialize billing agreements grid container
     *
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_billing_agreement';
        $this->_blockGroup = 'Magento_Paypal';
        $this->_headerText = __('Billing Agreements');
        parent::_construct();
        $this->_removeButton('add');
    }
}
