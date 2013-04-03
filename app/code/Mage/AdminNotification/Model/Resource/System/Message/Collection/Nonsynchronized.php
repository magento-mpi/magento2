<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

class Mage_AdminNotification_Model_Resource_System_Message_Collection_Nonsynchronized
    extends Mage_AdminNotification_Model_Resource_System_Message_Collection
{
    /**
     * Resource collection initialization
     */
    protected function _construct()
    {
        $this->_init(
            'Mage_AdminNotification_Model_System_Message', 'Mage_AdminNotification_Model_Resource_System_Message'
        );
    }
}
