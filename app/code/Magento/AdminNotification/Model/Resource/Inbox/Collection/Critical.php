<?php
/**
 * Critical messages collection
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
            ['neq' => 1]
        )->addFieldToFilter(
            'is_remove',
            ['neq' => 1]
        )->addFieldToFilter(
            'severity',
            \Magento\Framework\Notification\MessageInterface::SEVERITY_CRITICAL
        )->setPageSize(
            1
        );
        return $this;
    }
}
