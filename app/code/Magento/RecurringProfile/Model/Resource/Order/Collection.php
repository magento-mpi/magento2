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

class Collection extends \Magento\Sales\Model\Resource\Order\Collection
{
    /**
     * Add filter by specified recurring profile id(s)
     *
     * @param array|int $ids
     * @return \Magento\RecurringProfile\Model\Resource\Order\Collection
     */
    public function addRecurringProfilesFilter($ids)
    {
        $ids = (is_array($ids)) ? $ids : array($ids);
        $this->getSelect()
            ->joinInner(
                array('srpo' => $this->getTable('recurring_profile_order')),
                'main_table.entity_id = srpo.order_id',
                array())
            ->where('srpo.profile_id IN(?)', $ids);
        return $this;
    }
}
