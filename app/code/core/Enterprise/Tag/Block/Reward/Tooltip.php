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
 * Advertising Tooltip block to show messages for gaining reward points when new tag submitted
 *
 * @category    Enterprise
 * @package     Enterprise_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Tag_Block_Reward_Tooltip extends Enterprise_Reward_Block_Tooltip
{
    /**
     * Init reward action instance
     *
     * @param string $action
     * @return Enterprise_Tag_Block_Reward_Tooltip
     */
    public function initRewardType($action)
    {
        /** @var $rewardHelper Enterprise_Reward_Helper_Data */
        $rewardHelper = Mage::helper('Enterprise_Reward_Helper_Data');
        if ($action && $rewardHelper->isEnabledOnFront()) {
            /** @var $session Mage_Customer_Model_Session */
            $session = Mage::getSingleton('Mage_Customer_Model_Session');
            $customer = $session->getCustomer();

            $this->_rewardInstance = Mage::getSingleton('Enterprise_Tag_Model_Reward');
            $this->_rewardInstance->setCustomer($customer)
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByCustomer();
            $this->_actionInstance = $this->_rewardInstance->getActionInstance($action, true);
        }

        return $this;
    }
}
