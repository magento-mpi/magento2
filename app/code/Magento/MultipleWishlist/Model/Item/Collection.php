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
 * Wishlist item collection filtered by customer
 */
namespace Magento\MultipleWishlist\Model\Item;

class Collection extends \Magento\MultipleWishlist\Model\Resource\Item\Collection
{
    /**
     * Initialize db select
     *
     * @return \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addCustomerIdFilter(\Mage::registry('current_customer')->getId())
            ->resetSortOrder()
            ->addDaysInWishlist()
            ->addStoreData();
        return $this;
    }
}
