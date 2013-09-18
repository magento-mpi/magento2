<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Controller;

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
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }

    /**
     * Remove Reward Points payment from current quote
     *
     */
    public function removeAction()
    {
        if (!$this->_objectManager->get('Magento\Reward\Helper\Data')->isEnabledOnFront()
            || !$this->_objectManager->get('Magento\Reward\Helper\Data')->getHasRates()) {
            return $this->_redirect('customer/account/');
        }

        $quote = \Mage::getSingleton('Magento\Checkout\Model\Session')->getQuote();

        if ($quote->getUseRewardPoints()) {
            $quote->setUseRewardPoints(false)->collectTotals()->save();
            \Mage::getSingleton('Magento\Checkout\Model\Session')->addSuccess(
                __('You removed the reward points from this order.')
            );
        } else {
            \Mage::getSingleton('Magento\Checkout\Model\Session')->addError(
                __('Reward points will not be used in this order.')
            );
        }

        $this->_redirect('checkout/cart');
    }
}
