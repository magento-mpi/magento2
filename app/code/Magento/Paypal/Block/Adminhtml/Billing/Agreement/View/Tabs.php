<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml billing agreements tabs view
 */
namespace Magento\Paypal\Block\Adminhtml\Billing\Agreement\View;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Initialize tab
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('billing_agreement_view_tabs');
        $this->setDestElementId('billing_agreement_view');
        $this->setTitle(__('Billing Agreement View'));
    }
}
