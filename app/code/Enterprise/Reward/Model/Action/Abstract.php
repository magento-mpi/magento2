<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Reward action model
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Enterprise_Reward_Model_Action_Abstract extends Magento_Object
{
    /**
     * Reward Instance
     * @var Enterprise_Reward_Model_Reward
     */
    protected $_reward;

    /**
     * Reward History Instance
     * @var Enterprise_Reward_Model_Reward_History
     */
    protected $_history;

    /**
     * Entity Instance
     * @var Magento_Object
     */
    protected $_entity;

    /**
     * Retrieve points delta for action
     *
     * @param int $websiteId
     * @return int
     */
    public function getPoints($websiteId)
    {
        return 0;
    }

    /**
     * Check whether rewards can be added for action
     *
     * @return bool
     */
    public function canAddRewardPoints()
    {
        if ($this->getEntity()) {
            $exist = $this->getHistory()->isExistHistoryUpdate(
                $this->getReward()->getCustomerId(),
                $this->getAction(),
                $this->getReward()->getWebsiteId(),
                $this->getEntity()->getId()
            );
        } else {
            $exist = false;
        }
        $exceeded = $this->isRewardLimitExceeded();
        return !$exist && !$exceeded;
    }

    /**
     * Check whether rewards limit is exceeded for action
     *
     * @return bool
     */
    public function isRewardLimitExceeded()
    {
        $limit = $this->getRewardLimit();
        if (!$limit) {
            return false;
        }
        $total = $this->getHistory()->getTotalQtyRewards(
            $this->getAction(), $this->getReward()->getCustomerId(), $this->getReward()->getWebsiteId()
        );

        if ($limit > $total) {
            return false;
        }
        return true;
    }

    /**
     * Return pre-configured limit of rewards for action
     * By default - without limitations
     *
     * @return int|string
     */
    public function getRewardLimit()
    {
        return 0;
    }

    /**
     * Estimate rewards available qty
     *
     * @return int|null
     */
    public function estimateRewardsQtyLimit()
    {
        $maxQty = (int)$this->getRewardLimit();
        if ($maxQty > 0) {
            $usedQty = (int)$this->getHistory()->getTotalQtyRewards(
                $this->getAction(), $this->getReward()->getCustomerId(), $this->getReward()->getWebsiteId()
            );
            return min(max($maxQty - $usedQty, 0), $maxQty);
        }
        return null;
    }

    /**
     * Return action message for history log
     *
     * @param array $args Additional history data
     * @return string
     */
    abstract public function getHistoryMessage($args = array());

    /**
     * Setter for $_reward
     *
     * @param Enterprise_Reward_Model_Reward $reward
     * @return Enterprise_Reward_Model_Action_Abstract
     */
    public function setReward($reward)
    {
        $this->_reward = $reward;
        return $this;
    }
    /**
     * Getter for $_reward
     *
     * @return Enterprise_Reward_Model_Reward
     */
    public function getReward()
    {
        return $this->_reward;
    }

    /**
     * Setter for $_history
     *
     * @param Enterprise_Reward_Model_Reward_History $history
     * @return Enterprise_Reward_Model_Action_Abstract
     */
    public function setHistory($history)
    {
        $this->_history = $history;
        return $this;
    }
    /**
     * Getter for $_history
     *
     * @return Enterprise_Reward_Model_Reward_History
     */
    public function getHistory()
    {
        return $this->_history;
    }

    /**
     * Setter for $_entity and assign entity Id to history
     *
     * @param Magento_Object $entity
     * @return Enterprise_Reward_Model_Action_Abstract
     */
    public function setEntity($entity)
    {
        $this->_entity = $entity;
        if ($this->getHistory() instanceof Magento_Object) {
            $this->getHistory()->setEntity($entity->getId());
        }
        return $this;
    }
    /**
     * Description goes here...
     *
     * @return Magento_Object
     */
    public function getEntity()
    {
        return $this->_entity;
    }
}
