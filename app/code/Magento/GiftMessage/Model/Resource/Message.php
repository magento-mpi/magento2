<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftMessage\Model\Resource;

/**
 * Gift Message resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Message extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('gift_message', 'gift_message_id');
    }
}
