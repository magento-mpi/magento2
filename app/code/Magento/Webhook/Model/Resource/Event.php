<?php
/**
 * Event resource
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Model\Resource;

class Event extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Initialize resource model
     */
    public function _construct()
    {
        $this->_init('webhook_event', 'event_id');
    }
}
