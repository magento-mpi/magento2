<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer balance controller for shopping cart
 *
 */
namespace Magento\CustomerBalance\Controller;

class Cart extends \Magento\Core\Controller\Front\Action
{
    /**
     * Only logged in users can use this functionality,
     * this function checks if user is logged in before all other actions
     *
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if (!\Mage::getSingleton('Magento\Customer\Model\Session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

    /**
     * Remove Store Credit from current quote
     *
     */
    public function removeAction()
    {
        if (!\Mage::helper('Magento\CustomerBalance\Helper\Data')->isEnabled()) {
            $this->_redirect('customer/account/');
            return;
        }

        $quote = \Mage::getSingleton('Magento\Checkout\Model\Session')->getQuote();
        if ($quote->getUseCustomerBalance()) {
            \Mage::getSingleton('Magento\Checkout\Model\Session')->addSuccess(
                __('The store credit payment has been removed from shopping cart.')
            );
            $quote->setUseCustomerBalance(false)->collectTotals()->save();
        } else {
            \Mage::getSingleton('Magento\Checkout\Model\Session')->addError(
                __('You are not using store credit in your shopping cart.')
            );
        }

        $this->_redirect('checkout/cart');
    }
}
