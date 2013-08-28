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
class Enterprise_Tag_Model_Reward_Observer extends Enterprise_Reward_Model_Observer
{
    /**
     * Tag data
     *
     * @var Enterprise_Tag_Helper_Data
     */
    protected $_tagData = null;

    /**
     * @param Enterprise_Tag_Helper_Data $tagData
     * @param Magento_Core_Helper_Data $coreData
     * @param Enterprise_Reward_Helper_Data $rewardData
     */
    public function __construct(
        Enterprise_Tag_Helper_Data $tagData,
        Magento_Core_Helper_Data $coreData,
        Enterprise_Reward_Helper_Data $rewardData
    ) {
        $this->_tagData = $tagData;
        parent::__construct($coreData, $rewardData);
    }

    /**
     * Send scheduled low balance warning notifications
     *
     * @return Enterprise_Tag_Model_Reward_Observer
     */
    public function scheduledBalanceExpireNotification()
    {
        /** @var $helper Enterprise_Tag_Helper_Data */
        $helper = $this->_tagData;
        $helper->addActionClassToRewardModel();

        return parent::scheduledBalanceExpireNotification();
    }
}