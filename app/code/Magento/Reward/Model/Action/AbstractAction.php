<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Reward action model
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Model\Action;

abstract class AbstractAction extends \Magento\Object
{
    /**
     * Reward Instance
     * @var \Magento\Reward\Model\Reward
     */
    protected $_reward;

    /**
     * Reward History Instance
     * @var \Magento\Reward\Model\Reward\History
     */
    protected $_history;

    /**
     * Entity Instance
     * @var \Magento\Object
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
            $this->getAction(),
            $this->getReward()->getCustomerId(),
            $this->getReward()->getWebsiteId()
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
                $this->getAction(),
                $this->getReward()->getCustomerId(),
                $this->getReward()->getWebsiteId()
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
     * @param \Magento\Reward\Model\Reward $reward
     * @return $this
     */
    public function setReward($reward)
    {
        $this->_reward = $reward;
        return $this;
    }

    /**
     * Getter for $_reward
     *
     * @return \Magento\Reward\Model\Reward
     */
    public function getReward()
    {
        return $this->_reward;
    }

    /**
     * Setter for $_history
     *
     * @param \Magento\Reward\Model\Reward\History $history
     * @return $this
     */
    public function setHistory($history)
    {
        $this->_history = $history;
        return $this;
    }

    /**
     * Getter for $_history
     *
     * @return \Magento\Reward\Model\Reward\History
     */
    public function getHistory()
    {
        return $this->_history;
    }

    /**
     * Setter for $_entity and assign entity Id to history
     *
     * @param \Magento\Object $entity
     * @return $this
     */
    public function setEntity($entity)
    {
        $this->_entity = $entity;
        if ($this->getHistory() instanceof \Magento\Object) {
            $this->getHistory()->setEntity($entity->getId());
        }
        return $this;
    }

    /**
     * Description goes here...
     *
     * @return \Magento\Object
     */
    public function getEntity()
    {
        return $this->_entity;
    }
}
