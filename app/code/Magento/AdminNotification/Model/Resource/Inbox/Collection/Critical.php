<?php
/**
 * Critical messages collection
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdminNotification\Model\Resource\Inbox\Collection;

class Critical extends \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Resource collection initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\AdminNotification\Model\Inbox', 'Magento\AdminNotification\Model\Resource\Inbox');
    }

    /**
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addOrder(
            'notification_id',
            self::SORT_ORDER_DESC
        )->addFieldToFilter(
            'is_read',
            array('neq' => 1)
        )->addFieldToFilter(
            'is_remove',
            array('neq' => 1)
        )->addFieldToFilter(
            'severity',
            \Magento\Framework\Notification\MessageInterface::SEVERITY_CRITICAL
        )->setPageSize(
            1
        );
        return $this;
    }
}
