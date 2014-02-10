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
namespace Magento\RecurringProfile\Model\Resource\Order;

class CollectionFilter
{
    /**
     * Add filter by specified recurring profile id(s)
     *
     * @param \Magento\Core\Model\Resource\Db\Collection\AbstractCollection $collection
     * @param array|int $ids
     * @return \Magento\RecurringProfile\Model\Resource\Order\Collection
     */
    public function byIds($collection, $ids)
    {
        $ids = (is_array($ids)) ? $ids : array($ids);
        $collection->getSelect()
            ->joinInner(
                array('rpo' => $collection->getTable('recurring_profile_order')),
                'main_table.entity_id = rpo.order_id',
                array())
            ->where('rpo.profile_id IN(?)', $ids);
        return $collection;
    }
}
