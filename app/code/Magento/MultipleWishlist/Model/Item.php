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
 * Enterprise wishlist item
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\MultipleWishlist\Model;

class Item extends \Magento\Wishlist\Model\Item
{
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Magento\MultipleWishlist\Model\Resource\Item');
    }
}
