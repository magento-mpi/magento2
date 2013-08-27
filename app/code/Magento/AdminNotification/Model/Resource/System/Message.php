<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

class Magento_AdminNotification_Model_Resource_System_Message extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Flag that notifies whether Primary key of table is auto-incremeted
     *
     * @var bool
     */
    protected $_isPkAutoIncrement = false;

    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('admin_system_messages', 'identity');
    }
}
