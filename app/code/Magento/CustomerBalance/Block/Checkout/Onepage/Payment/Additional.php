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
 * Customer balance as an additional payment option during checkout
 *
 * @category   Magento
 * @package    Magento_CustomerBalance
 */
namespace Magento\CustomerBalance\Block\Checkout\Onepage\Payment;

class Additional extends \Magento\Core\Block\Template
{
    /**
     * Customer balance instance
     *
     * @var \Magento\CustomerBalance\Model\Balance
     */
    protected $_balanceModel = null;

    /**
     * Get quote instance
     *
     * @return \Magento\Sales\Model\Quote
     */
    protected function _getQuote()
    {
        return \Mage::getSingleton('Magento\Checkout\Model\Session')->getQuote();
    }

    /**
     * Getter
     *
     * @return \Magento\Sales\Model\Quote
     */
    public function getQuote()
    {
        return $this->_getQuote();
    }

    /**
     * Get balance instance
     *
     * @return \Magento\CustomerBalance\Model\Balance
     */
    protected function _getBalanceModel()
    {
        if (is_null($this->_balanceModel)) {
            $this->_balanceModel = \Mage::getModel('\Magento\CustomerBalance\Model\Balance')
                ->setCustomer($this->_getCustomer())
                ->setWebsiteId(\Mage::app()->getStore()->getWebsiteId());

            //load customer balance for customer in case we have
            //registered customer and this is not guest checkout
            if ($this->_getCustomer()->getId()) {
                $this->_balanceModel->loadByCustomer();
            }
        }
        return $this->_balanceModel;
    }

    /**
     * Get customer instance
     *
     * @return \Magento\Customer\Model\Customer
     */
    protected function _getCustomer()
    {
        return \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomer();
    }

    /**
     * Can display customer balance container
     *
     * @return bool
     */
    public function isDisplayContainer()
    {
        if (!$this->_getCustomer()->getId()) {
            return false;
        }

        if (!$this->getBalance()) {
            return false;
        }

        return true;
    }

    /**
     * Check whether customer balance is allowed as additional payment option
     *
     * @return bool
     */
    public function isAllowed()
    {
        if (!$this->isDisplayContainer()) {
            return false;
        }

        if (!$this->getAmountToCharge()) {
            return false;
        }

        return true;
    }

    /**
     * Get balance amount
     *
     * @return float
     */
    public function getBalance()
    {
        if (!$this->_getCustomer()->getId()) {
            return 0;
        }
        return $this->_getBalanceModel()->getAmount();
    }

    /**
     * Get balance amount to be charged
     *
     * @return float
     */
    public function getAmountToCharge()
    {
        if ($this->isCustomerBalanceUsed()) {
            return $this->_getQuote()->getCustomerBalanceAmountUsed();
        }

        return min($this->getBalance(), $this->_getQuote()->getBaseGrandTotal());
    }

    /**
     * Check whether customer balance is used in current quote
     *
     * @return bool
     */
    public function isCustomerBalanceUsed() {
        return $this->_getQuote()->getUseCustomerBalance();
    }

    /**
     * Check whether customer balance fully covers quote
     *
     * @return bool
     */
    public function isFullyPaidAfterApplication()
    {
        return $this->_getBalanceModel()->isFullAmountCovered($this->_getQuote(), true);
    }
}
