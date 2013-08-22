<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdminNotification
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Collection of unread notifications
 *
 * @category    Magento
 * @package     Magento_AdminNotification
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_AdminNotification_Model_Resource_Inbox_Collection_Unread
    extends Magento_AdminNotification_Model_Resource_Inbox_Collection
{
    /**
     * Init collection select
     *
     * @return Magento_AdminNotification_Model_Resource_Inbox_Collection_Unread
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
