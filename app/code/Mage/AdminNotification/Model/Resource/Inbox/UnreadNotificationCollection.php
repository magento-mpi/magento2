<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_AdminNotification
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Collection of unread notifications
 *
 * @category    Mage
 * @package     Mage_AdminNotification
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_AdminNotification_Model_Resource_Inbox_UnreadNotificationCollection
    extends Mage_AdminNotification_Model_Resource_Inbox_Collection
{
    /**
     * Init collection select
     *
     * @return Mage_AdminNotification_Model_Resource_Inbox_LatestUnreadCollection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addFilter('is_remove', 0);
        $this->addFilter('is_read', 0);
        $this->setOrder('date_added');
        return $this;
    }
}
