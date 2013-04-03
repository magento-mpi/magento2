<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

class Mage_AdminNotification_Model_Resource_System_Message extends Mage_Core_Model_Resource_Db_Abstract
{
    protected $_isPkAutoIncrement = false;
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('admin_system_messages', 'identity');
    }
}
