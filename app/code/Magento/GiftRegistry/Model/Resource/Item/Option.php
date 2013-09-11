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
namespace Magento\GiftRegistry\Model\Resource\Item;

class Option extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        $this->_init('magento_giftregistry_item_option', 'option_id');
    }
}
