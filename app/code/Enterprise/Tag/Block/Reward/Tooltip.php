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
     * Array of data helpers
     *
     * @var array
     */
    protected $_helpers;

    /**
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(Magento_Core_Block_Template_Context $context, array $data = array())
    {
        parent::__construct($context, $data);

        if (isset($data['helpers'])) {
            $this->_helpers = $data['helpers'];
        }

        /** @var $helper Enterprise_Tag_Helper_Data */
        $helper = $this->_helper('Enterprise_Tag_Helper_Data');
        $helper->addActionClassToRewardModel();
    }

    /**
     * Helper getter
     *
     * @param string $helperName
     * @return Magento_Core_Helper_Abstract
     */
    protected function _helper($helperName)
    {
        return isset($this->_helpers[$helperName]) ? $this->_helpers[$helperName] : Mage::helper($helperName);
    }
}
