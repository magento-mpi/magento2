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
 * Reward action for review submission
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Model_Action_Review extends Enterprise_Reward_Model_Action_Abstract
{
    /**
     * Reward data
     *
     * @var Enterprise_Reward_Helper_Data
     */
    protected $_rewardData = null;

    /**
     * Constructor
     *
     * By default is looking for first argument as array and assigns it as object
     * attributes This behavior may change in child classes
     *
     * @param Enterprise_Reward_Helper_Data $rewardData
     * @param array $data
     */
    public function __construct(
        Enterprise_Reward_Helper_Data $rewardData,
        array $data = array()
    ) {
        $this->_rewardData = $rewardData;
        parent::__construct($data);
    }

    /**
     * Retrieve points delta for action
     *
     * @param int $websiteId
     * @return int
     */
    public function getPoints($websiteId)
    {
        return (int)$this->_rewardData->getPointsConfig('review', $websiteId);
    }

    /**
     * Return pre-configured limit of rewards for action
     *
     * @return int|string
     */
    public function getRewardLimit()
    {
        return $this->_rewardData->getPointsConfig(
            'review_limit',
            $this->getReward()->getWebsiteId()
        );
    }

    /**
     * Return action message for history log
     *
     * @param array $args Additional history data
     * @return string
     */
    public function getHistoryMessage($args = array())
    {
        return __('For submitting a product review');
    }
}
