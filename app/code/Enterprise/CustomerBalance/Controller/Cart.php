<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer balance controller for shopping cart
 *
 */
class Enterprise_CustomerBalance_Controller_Cart extends Magento_Core_Controller_Front_Action
{
    /**
     * Only logged in users can use this functionality,
     * this function checks if user is logged in before all other actions
     *
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getSingleton('Magento_Customer_Model_Session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

    /**
     * Remove Store Credit from current quote
     *
     */
    public function removeAction()
    {
        if (!$this->_objectManager->get('Enterprise_CustomerBalance_Helper_Data')->isEnabled()) {
            $this->_redirect('customer/account/');
            return;
        }

        $quote = Mage::getSingleton('Magento_Checkout_Model_Session')->getQuote();
        if ($quote->getUseCustomerBalance()) {
            Mage::getSingleton('Magento_Checkout_Model_Session')->addSuccess(
                __('The store credit payment has been removed from shopping cart.')
            );
            $quote->setUseCustomerBalance(false)->collectTotals()->save();
        } else {
            Mage::getSingleton('Magento_Checkout_Model_Session')->addError(
                __('You are not using store credit in your shopping cart.')
            );
        }

        $this->_redirect('checkout/cart');
    }
}
