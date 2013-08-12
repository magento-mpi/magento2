<?php
/**
 * Event resource Collection
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Resource_Event_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('Magento_Webhook_Model_Event', 'Magento_Webhook_Model_Resource_Event');
    }
}
