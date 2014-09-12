<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerBalance\Model\Total\Quote;

class Customerbalance extends \Magento\Sales\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * Customer balance data
     *
     * @var \Magento\CustomerBalance\Helper\Data
     */
    protected $_customerBalanceData = null;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\CustomerBalance\Model\BalanceFactory
     */
    protected $_balanceFactory;

    /**
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\CustomerBalance\Model\BalanceFactory $balanceFactory
     * @param \Magento\CustomerBalance\Helper\Data $customerBalanceData
     */
    public function __construct(
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\CustomerBalance\Model\BalanceFactory $balanceFactory,
        \Magento\CustomerBalance\Helper\Data $customerBalanceData
    ) {
        $this->_storeManager = $storeManager;
        $this->_balanceFactory = $balanceFactory;
        $this->_customerBalanceData = $customerBalanceData;
        $this->setCode('customerbalance');
    }

    /**
     * Collect customer balance totals for specified address
     *
     * @param \Magento\Sales\Model\Quote\Address $address
     * @return \Magento\CustomerBalance\Model\Total\Quote\Customerbalance
     */
    public function collect(\Magento\Sales\Model\Quote\Address $address)
    {
        if (!$this->_customerBalanceData->isEnabled()) {
            return $this;
        }
        $quote = $address->getQuote();
        if (!$quote->getCustomerBalanceCollected()) {
            $quote->setBaseCustomerBalAmountUsed(0);
            $quote->setCustomerBalanceAmountUsed(0);

            $quote->setCustomerBalanceCollected(true);
        }

        $baseTotalUsed = $totalUsed = $baseUsed = $used = 0;

        $baseBalance = $balance = 0;
        if ($quote->getCustomer()->getId()) {
            if ($quote->getUseCustomerBalance()) {
                $store = $this->_storeManager->getStore($quote->getStoreId());
                $baseBalance = $this->_balanceFactory->create()->setCustomer(
                    $quote->getCustomer()
                )->setCustomerId(
                    $quote->getCustomer()->getId()
                )->setWebsiteId(
                    $store->getWebsiteId()
                )->loadByCustomer()->getAmount();
                $balance = $quote->getStore()->convertPrice($baseBalance);
            }
        }

        $baseAmountLeft = $baseBalance - $quote->getBaseCustomerBalAmountUsed();
        $amountLeft = $balance - $quote->getCustomerBalanceAmountUsed();

        if ($baseAmountLeft >= $address->getBaseGrandTotal()) {
            $baseUsed = $address->getBaseGrandTotal();
            $used = $address->getGrandTotal();

            $address->setBaseGrandTotal(0);
            $address->setGrandTotal(0);
        } else {
            $baseUsed = $baseAmountLeft;
            $used = $amountLeft;

            $address->setBaseGrandTotal($address->getBaseGrandTotal() - $baseAmountLeft);
            $address->setGrandTotal($address->getGrandTotal() - $amountLeft);
        }

        $baseTotalUsed = $quote->getBaseCustomerBalAmountUsed() + $baseUsed;
        $totalUsed = $quote->getCustomerBalanceAmountUsed() + $used;

        $quote->setBaseCustomerBalAmountUsed($baseTotalUsed);
        $quote->setCustomerBalanceAmountUsed($totalUsed);

        $address->setBaseCustomerBalanceAmount($baseUsed);
        $address->setCustomerBalanceAmount($used);

        return $this;
    }

    /**
     * Return shopping cart total row items
     *
     * @param \Magento\Sales\Model\Quote\Address $address
     * @return \Magento\CustomerBalance\Model\Total\Quote\Customerbalance
     */
    public function fetch(\Magento\Sales\Model\Quote\Address $address)
    {
        if (!$this->_customerBalanceData->isEnabled()) {
            return $this;
        }
        if ($address->getCustomerBalanceAmount()) {
            $address->addTotal(
                array(
                    'code' => $this->getCode(),
                    'title' => __('Store Credit'),
                    'value' => -$address->getCustomerBalanceAmount()
                )
            );
        }
        return $this;
    }
}
