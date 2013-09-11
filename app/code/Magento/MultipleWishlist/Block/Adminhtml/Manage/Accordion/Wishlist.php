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
namespace Magento\MultipleWishlist\Block\Adminhtml\Manage\Accordion;

class Wishlist
    extends \Magento\AdvancedCheckout\Block\Adminhtml\Manage\Accordion\Wishlist
{
    /**
     * Return items collection
     *
     * @return \Magento\Wishlist\Model\Resource\Item\Collection
     */
    protected function _createItemsCollection()
    {
        return \Mage::getModel('\Magento\MultipleWishlist\Model\Item')->getCollection();
    }

    /**
     * Prepare Grid columns
     *
     * @return \Magento\MultipleWishlist\Block\Adminhtml\Manage\Accordion\Wishlist
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
