<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * "Reward Points" link
 */
class Magento_Reward_Block_Customer_AccountLink extends Magento_Page_Block_Link_Current
{
    /** @var Magento_Reward_Helper_Data */
    protected $_rewardHelper;

    /**
     * Constructor
     *
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Reward_Helper_Data $rewardHelper
     * @param Magento_Core_Helper_Data $coreData
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Template_Context $context,
        Magento_Reward_Helper_Data $rewardHelper,
        Magento_Core_Helper_Data $coreData,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_rewardHelper = $rewardHelper;
    }

    /**
     * @inheritdoc
     */
    protected function _toHtml()
    {
        if ($this->_rewardHelper->isEnabledOnFront()) {
            return parent::_toHtml();
        } else {
            return '';
        }
    }
}
