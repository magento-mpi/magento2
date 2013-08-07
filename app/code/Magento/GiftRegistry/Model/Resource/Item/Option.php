<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift registry item option resource model
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_GiftRegistry_Model_Resource_Item_Option extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        $this->_init('magento_giftregistry_item_option', 'option_id');
    }
}
