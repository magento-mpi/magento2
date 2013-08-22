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
class Enterprise_Reward_Block_Customer_Account extends Magento_Core_Block_Abstract
{
    /**
     * Add RP link to tab if we have all rates
     *
     * @return Enterprise_Reward_Block_Customer_Account
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        /** @var $navigationBlock Magento_Customer_Block_Account_Navigation */
        $navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock && Mage::helper('Enterprise_Reward_Helper_Data')->isEnabledOnFront()) {
            $navigationBlock->addLink('enterprise_reward', 'enterprise_reward/customer/info/',
                __('Reward Points')
            );
        }
        return $this;
    }
}
