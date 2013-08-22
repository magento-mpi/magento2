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
 * Enterprise wishlist item
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Wishlist_Model_Item extends Magento_Wishlist_Model_Item
{
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Enterprise_Wishlist_Model_Resource_Item');
    }
}
