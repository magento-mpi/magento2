<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Reward\Model\Action;

/**
 * Reward action for using points to purchase order
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Order extends \Magento\Reward\Model\Action\AbstractAction
{
    /**
     * Return action message for history log
     *
     * @param array $args Additional history data
     * @return string
     */
    public function getHistoryMessage($args = [])
    {
        $incrementId = isset($args['increment_id']) ? $args['increment_id'] : '';
        return __('Redeemed for order #%1', $incrementId);
    }

    /**
     * Setter for $_entity and add some extra data to history
     *
     * @param \Magento\Framework\Object $entity
     * @return $this
     */
    public function setEntity($entity)
    {
        parent::setEntity($entity);
        $this->getHistory()->addAdditionalData(['increment_id' => $this->getEntity()->getIncrementId()]);
        return $this;
    }
}
