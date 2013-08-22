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
    /**
     * @inheritdoc
     */
    protected function _toHtml()
    {
        if (Mage::helper('Enterprise_Reward_Helper_Data')->isEnabledOnFront()) {
            return parent::_toHtml();
        } else {
            return '';
        }
    }
}
