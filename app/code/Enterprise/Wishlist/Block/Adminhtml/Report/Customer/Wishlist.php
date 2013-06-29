<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wishlist report block
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Wishlist_Block_Adminhtml_Report_Customer_Wishlist
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Enterprise_Wishlist';
        $this->_controller = 'adminhtml_report_customer_wishlist';
        $this->_headerText = Mage::helper('Enterprise_Wishlist_Helper_Data')->__("Customer's Wish List Report");
        parent::_construct();
        $this->_removeButton('add');
    }
}
