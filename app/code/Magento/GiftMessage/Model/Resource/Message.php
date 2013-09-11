<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftMessage
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Gift Message resource model
 *
 * @category    Magento
 * @package     Magento_GiftMessage
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftMessage\Model\Resource;

class Message extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_init('gift_message', 'gift_message_id');
    }
}
