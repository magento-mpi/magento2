<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wishlist item option resource model
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Wishlist\Model\Resource\Item;

class Option extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('wishlist_item_option', 'option_id');
    }
}
