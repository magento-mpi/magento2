<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Reward action for reverting points
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Model\Action;

class OrderRevert extends \Magento\Reward\Model\Action\AbstractAction
{
    /**
     * Check whether rewards can be added for action
     *
     * @return bool
     */
    public function canAddRewardPoints()
    {
        return true;
    }

    /**
     * Return action message for history log
     *
     * @param   array $args Additional history data
     * @return  string
     */
    public function getHistoryMessage($args = array())
    {
        $incrementId = isset($args['increment_id']) ? $args['increment_id'] : '';

        return __('Reverted from order #%1', $incrementId);
    }

    /**
     * Setter for $_entity and add some extra data to history
     *
     * @param   \Magento\Framework\Object $entity
     * @return  \Magento\Reward\Model\Action\AbstractAction
     */
    public function setEntity($entity)
    {
        parent::setEntity($entity);
        $this->getHistory()->addAdditionalData(array('increment_id' => $this->getEntity()->getIncrementId()));

        return $this;
    }
}
