<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Reward action for tag submission
 *
 * @category    Enterprise
 * @package     Enterprise_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Tag_Model_Reward_Action_Tag extends Enterprise_Reward_Model_Action_Abstract
{
    /**
     * Retrieve points delta for action
     *
     * @param int $websiteId
     * @return int
     */
    public function getPoints($websiteId)
    {
        /** @var $helper Enterprise_Reward_Helper_Data */
        $helper = Mage::helper('Enterprise_Reward_Helper_Data');
        return (int) $helper->getPointsConfig('tag', $websiteId);
    }

    /**
     * Return pre-configured limit of rewards for action
     *
     * @return int|string
     */
    public function getRewardLimit()
    {
        /** @var $helper Enterprise_Reward_Helper_Data */
        $helper = Mage::helper('Enterprise_Reward_Helper_Data');
        return $helper->getPointsConfig('tag_limit', $this->getReward()->getWebsiteId());
    }

    /**
     * Return action message for history log
     *
     * @param array $args Additional history data
     * @return string
     */
    public function getHistoryMessage($args = array())
    {
        $tag = isset($args['tag']) ? $args['tag'] : '';
        return __('For submitting tag (%1)', $tag);
    }

    /**
     * Setter for $_entity and add some extra data to history
     *
     * @param Magento_Object $entity
     * @return Enterprise_Reward_Model_Action_Abstract
     */
    public function setEntity($entity)
    {
        parent::setEntity($entity);
        $this->getHistory()->addAdditionalData(array(
            'tag' => $this->getEntity()->getName()
        ));
        return $this;
    }
}
