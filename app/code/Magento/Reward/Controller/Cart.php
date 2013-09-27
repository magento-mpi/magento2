<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Reward_Controller_Cart extends Magento_Core_Controller_Front_Action
{
    /**
     * Only logged in users can use this functionality,
     * this function checks if user is logged in before all other actions
     *
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if (!$this->_objectManager->get('Magento_Customer_Model_Session')->authenticate($this)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }

    /**
     * Remove Reward Points payment from current quote
     *
     */
    public function removeAction()
    {
        if (!$this->_objectManager->get('Magento_Reward_Helper_Data')->isEnabledOnFront()
            || !$this->_objectManager->get('Magento_Reward_Helper_Data')->getHasRates()) {
            return $this->_redirect('customer/account/');
        }

        $quote = $this->_objectManager->get('Magento_Checkout_Model_Session')->getQuote();

        if ($quote->getUseRewardPoints()) {
            $quote->setUseRewardPoints(false)->collectTotals()->save();
            $this->_objectManager->get('Magento_Checkout_Model_Session')->addSuccess(
                __('You removed the reward points from this order.')
            );
        } else {
            $this->_objectManager->get('Magento_Checkout_Model_Session')->addError(
                __('Reward points will not be used in this order.')
            );
        }

        $this->_redirect('checkout/cart');
    }
}
