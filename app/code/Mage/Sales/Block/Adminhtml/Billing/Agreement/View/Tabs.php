<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml billing agreements tabs view
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Block_Adminhtml_Billing_Agreement_View_Tabs extends Mage_Backend_Block_Widget_Tabs
{
    /**
     * Initialize tab
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('billing_agreement_view_tabs');
        $this->setDestElementId('billing_agreement_view');
        $this->setTitle(__('Billing Agreement View'));
    }
}
