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
 * Checkout Tooltip block to show checkout cart message for gaining reward points
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Block_Tooltip_Checkout extends Enterprise_Reward_Block_Tooltip
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
