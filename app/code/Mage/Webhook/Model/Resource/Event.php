<?php
/**
 * Event resource
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Resource_Event extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize resource model
     */
    public function _construct()
    {
        $this->_init('webhook_event', 'event_id');
    }
}
