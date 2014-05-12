<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\MultipleWishlist\Model;

/**
 * Enterprise wishlist item
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Item extends \Magento\Wishlist\Model\Item
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\MultipleWishlist\Model\Resource\Item');
    }
}
