<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Checkout Tooltip block to show checkout cart message for gaining reward points
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reward_Block_Tooltip_Checkout extends Magento_Reward_Block_Tooltip
{
    /**
     * Set quote to the reward action instance
     *
     * @param int|string $action
     */
    public function initRewardType($action)
    {
        parent::initRewardType($action);
        if ($this->_actionInstance) {
            $this->_actionInstance->setQuote(Mage::getSingleton('Magento_Checkout_Model_Session')->getQuote());
        }
    }
}
