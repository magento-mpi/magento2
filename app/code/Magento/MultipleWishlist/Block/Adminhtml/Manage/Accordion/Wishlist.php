<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Accordion grid for products in wishlist
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_MultipleWishlist_Block_Adminhtml_Manage_Accordion_Wishlist
    extends Magento_AdvancedCheckout_Block_Adminhtml_Manage_Accordion_Wishlist
{
    /**
     * Return items collection
     *
     * @return Magento_Wishlist_Model_Resource_Item_Collection
     */
    protected function _createItemsCollection()
    {
        return Mage::getModel('Magento_MultipleWishlist_Model_Item')->getCollection();
    }

    /**
     * Prepare Grid columns
     *
     * @return Magento_MultipleWishlist_Block_Adminhtml_Manage_Accordion_Wishlist
     */
    protected function _prepareColumns()
    {
        $this->addColumn('wishlist_name', array(
            'header'    => __('Wishlist'),
            'index'     => 'wishlist_name',
            'sortable'  => false
        ));

        return parent::_prepareColumns();
    }

}
