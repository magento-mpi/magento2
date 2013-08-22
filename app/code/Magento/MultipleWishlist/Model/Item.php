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
class Magento_MultipleWishlist_Model_Item extends Magento_Wishlist_Model_Item
{
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Magento_MultipleWishlist_Model_Resource_Item');
    }
}
