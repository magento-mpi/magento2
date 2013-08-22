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
 * Tag reward model observer
 *
 * @category    Enterprise
 * @package     Enterprise_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Tag_Model_Reward_Observer extends Magento_Reward_Model_Observer
{
    /**
     * Send scheduled low balance warning notifications
     *
     * @return Enterprise_Tag_Model_Reward_Observer
     */
    public function scheduledBalanceExpireNotification()
    {
        /** @var $helper Enterprise_Tag_Helper_Data */
        $helper = Mage::helper('Enterprise_Tag_Helper_Data');
        $helper->addActionClassToRewardModel();

        return parent::scheduledBalanceExpireNotification();
    }
}