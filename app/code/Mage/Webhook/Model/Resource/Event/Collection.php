<?php
/**
 * Event resource Collection
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Resource_Event_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('Mage_Webhook_Model_Event', 'Mage_Webhook_Model_Resource_Event');
    }
}
