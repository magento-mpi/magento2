<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales order create sidebar viewed block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Viewed extends Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Abstract
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_sidebar_viewed');
        $this->setDataId('viewed');
    }

    /**
     * Retrieve display block availability
     *
     * @return bool
     */
    public function canDisplay()
    {
        return false;
    }

    /**
     * Retrieve availability removing items in block
     *
     * @return bool
     */
    public function canRemoveItems()
    {
        return false;
    }

    public function getHeaderText()
    {
        return __('Recently Viewed');
    }

}
