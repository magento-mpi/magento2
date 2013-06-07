<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Saas_JobNotification_Model_Resource_Notification_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource collection initialization
     */
    protected function _construct()
    {
        $this->_init(
            'Saas_JobNotification_Model_Notification', 'Saas_JobNotification_Model_Resource_Notification'
        );
    }

    /**
     * Initialize db query
     *
     * @return Saas_JobNotification_Model_Resource_Notification_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addFilter('is_remove', '0');
        return $this;
    }
}
