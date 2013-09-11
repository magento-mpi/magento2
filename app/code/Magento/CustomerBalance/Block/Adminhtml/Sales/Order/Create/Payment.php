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
 * Customer balance block for order creation page
 *
 */
namespace Magento\CustomerBalance\Block\Adminhtml\Sales\Order\Create;

class Payment
extends \Magento\Core\Block\Template
{
    /**
     * @var \Magento\CustomerBalance\Model\Balance
     */
    protected $_balanceInstance;

    /**
     * Retrieve order create model
     *
     * @return \Magento\Adminhtml\Model\Sales\Order\Create
     */
    protected function _getOrderCreateModel()
    {
        return \Mage::getSingleton('Magento\Adminhtml\Model\Sales\Order\Create');
    }

    /**
     * Return store manager instance
     *
     * @return \Magento\Core\Model\StoreManager
     */
    protected function _getStoreManagerModel()
    {
        return \Mage::getSingleton('Magento\Core\Model\StoreManager');
    }

    /**
     * Format value as price
     *
     * @param numeric $value
     * @return string
     */
    public function formatPrice($value)
    {
        return \Mage::getSingleton('Magento\Adminhtml\Model\Session\Quote')->getStore()->formatPrice($value);
    }

    /**
     * Balance getter
     *
     * @param bool $convertPrice
     * @return float
     */
    public function getBalance($convertPrice = false)
    {
        if (!$this->_helperFactory->get('Magento\CustomerBalance\Helper\Data')->isEnabled() || !$this->_getBalanceInstance()) {
            return 0.0;
        }
        if ($convertPrice) {
            return $this->_getStoreManagerModel()->getStore($this->_getOrderCreateModel()->getQuote()->getStoreId())
                ->convertPrice($this->_getBalanceInstance()->getAmount());
        }
        return $this->_getBalanceInstance()->getAmount();
    }

    /**
     * Check whether quote uses customer balance
     *
     * @return bool
     */
    public function getUseCustomerBalance()
    {
        return $this->_getOrderCreateModel()->getQuote()->getUseCustomerBalance();
    }

    /**
     * Check whether customer balance fully covers quote
     *
     * @return bool
     */
    public function isFullyPaid()
    {
        if (!$this->_getBalanceInstance()) {
            return false;
        }
        return $this->_getBalanceInstance()->isFullAmountCovered($this->_getOrderCreateModel()->getQuote());
    }

    /**
     * Check whether quote uses customer balance
     *
     * @return bool
     */
    public function isUsed()
    {
        return $this->getUseCustomerBalance();
    }

    /**
     * Instantiate/load balance and return it
     *
     * @return \Magento\CustomerBalance\Model\Balance|false
     */
    protected function _getBalanceInstance()
    {
        if (!$this->_balanceInstance) {
            $quote = $this->_getOrderCreateModel()->getQuote();
            if (!$quote || !$quote->getCustomerId() || !$quote->getStoreId()) {
                return false;
            }

            $store = \Mage::app()->getStore($quote->getStoreId());
            $this->_balanceInstance = \Mage::getModel('\Magento\CustomerBalance\Model\Balance')
                ->setCustomerId($quote->getCustomerId())
                ->setWebsiteId($store->getWebsiteId())
                ->loadByCustomer();
        }
        return $this->_balanceInstance;
    }

    /**
     * Whether customer store credit balance could be used
     *
     * @return bool
     */
    public function canUseCustomerBalance()
    {
        $quote = $this->_getOrderCreateModel()->getQuote();
        return $this->getBalance() && ($quote->getBaseGrandTotal() + $quote->getBaseCustomerBalanceAmountUsed() > 0);
    }
}
