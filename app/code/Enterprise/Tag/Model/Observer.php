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
 * Tag module observer
 *
 * @category    Enterprise
 * @package     Enterprise_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Tag_Model_Observer
{
    /**
     * Add tag action class to reward model
     *
     * @return Enterprise_Tag_Model_Observer
     */
    public function addActionClassToRewardModel()
    {
        /** @var $helper Enterprise_Tag_Helper_Data */
        $helper = Mage::helper('Enterprise_Tag_Helper_Data');
        $helper->addActionClassToRewardModel();

        return $this;
    }

    /**
     * Update points balance after tag submit
     *
     * @param Magento_Event_Observer $observer
     * @return Enterprise_Tag_Model_Observer
     */
    public function updateRewardPointsWhenTagSubmit($observer)
    {
        /* @var $tag Magento_Tag_Model_Tag */
        $tag = $observer->getEvent()->getObject();
        $websiteId = Mage::app()->getStore($tag->getFirstStoreId())->getWebsiteId();

        /** @var $rewardHelper Enterprise_Reward_Helper_Data */
        $rewardHelper = Mage::helper('Enterprise_Reward_Helper_Data');
        if (!$rewardHelper->isEnabledOnFront($websiteId)) {
            return $this;
        }
        if (($tag->getApprovedStatus() == $tag->getStatus()) && $tag->getFirstCustomerId()) {
            /** @var $reward Enterprise_Tag_Model_Reward */
            $reward = Mage::getModel('Enterprise_Tag_Model_Reward');
            $reward->setCustomerId($tag->getFirstCustomerId())
                ->setStore($tag->getFirstStoreId())
                ->setAction(Enterprise_Tag_Model_Reward::REWARD_ACTION_TAG)
                ->setActionEntity($tag)
                ->updateRewardPoints();
        }
        return $this;
    }
}
