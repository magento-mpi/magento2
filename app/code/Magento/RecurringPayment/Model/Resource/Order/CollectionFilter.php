<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Flat sales order collection
 */
namespace Magento\RecurringPayment\Model\Resource\Order;

class CollectionFilter
{
    /**
     * Add filter by specified recurring payment id(s)
     *
     * @param \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection $collection
     * @param array|int $ids
     * @return \Magento\Sales\Model\Resource\Order\Collection
     */
    public function byIds($collection, $ids)
    {
        $ids = is_array($ids) ? $ids : [$ids];
        $collection->getSelect()->joinInner(
            ['rpo' => $collection->getTable('recurring_payment_order')],
            'main_table.entity_id = rpo.order_id',
            []
        )->where(
            'rpo.payment_id IN(?)',
            $ids
        );
        return $collection;
    }
}
