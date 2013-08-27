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
 * Customer Account empty block (using only just for adding RP link to tab)
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Block_Customer_AccountLink extends Mage_Page_Block_Link_Current
{
    /** @var Enterprise_Reward_Helper_Data */
    protected $_rewardHelper;

    /**
     * Constructor
     *
     * @param Mage_Core_Block_Template_Context $context
     * @param Enterprise_Reward_Helper_Data $rewardHelper
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Enterprise_Reward_Helper_Data $rewardHelper,
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
