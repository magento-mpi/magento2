<?php
/**
 * Critical messages collection
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_AdminNotification_Model_Resource_Inbox_Collection_Critical
    extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource collection initialization
     */
    protected function _construct()
    {
        $this->_init('Magento_AdminNotification_Model_Inbox', 'Magento_AdminNotification_Model_Resource_Inbox');
    }

    /**
     * @return $this|Magento_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addOrder('notification_id', self::SORT_ORDER_DESC)
            ->addFieldToFilter('is_read', array('neq' => 1))
            ->addFieldToFilter('is_remove', array('neq' => 1))
            ->addFieldToFilter('severity', Magento_AdminNotification_Model_Inbox::SEVERITY_CRITICAL)
            ->setPageSize(1);
        return $this;
    }
}
