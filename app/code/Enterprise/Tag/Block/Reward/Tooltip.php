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
     * @param Enterprise_Tag_Helper_Data $tagData
     * @param Magento_Core_Helper_Data $coreData
     * @param Enterprise_Reward_Helper_Data $rewardData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Enterprise_Tag_Helper_Data $tagData,
        Magento_Core_Helper_Data $coreData,
        Enterprise_Reward_Helper_Data $rewardData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        parent::__construct($rewardData, $coreData, $context, $data);

        $tagData->addActionClassToRewardModel();
    }
}
