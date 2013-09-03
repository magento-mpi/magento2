<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Account empty block (using only just for adding RP link to tab)
 *
 * @category    Enterprise
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
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
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Template_Context $context,
        Magento_Reward_Helper_Data $rewardHelper,
        array $data = array()
    ) {
        parent::__construct($context, $data);
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
