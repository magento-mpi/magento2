<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml billing agreement related orders tab
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Block_Adminhtml_Billing_Agreement_View_Tab_Orders extends Magento_Core_Block_Text_List
    implements Magento_Backend_Block_Widget_Tab_Interface
{

    /**
     * Initialize grid params
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('billing_agreement_orders');
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Related Orders');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Related Orders');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
