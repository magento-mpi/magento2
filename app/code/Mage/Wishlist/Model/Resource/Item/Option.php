<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wishlist item option resource model
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Wishlist_Model_Resource_Item_Option extends Magento_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('wishlist_item_option', 'option_id');
    }
}
