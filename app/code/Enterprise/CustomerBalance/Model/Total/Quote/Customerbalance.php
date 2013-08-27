<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Enterprise_CustomerBalance_Model_Total_Quote_Customerbalance extends Magento_Sales_Model_Quote_Address_Total_Abstract
{
    /**
     * Customer balance data
     *
     * @var Enterprise_CustomerBalance_Helper_Data
     */
    protected $_customerBalanceData = null;

    /**
     * Init total model, set total code
     *
     *
     *
     * @param Enterprise_CustomerBalance_Helper_Data $customerBalanceData
     */
    public function __construct(
        Enterprise_CustomerBalance_Helper_Data $customerBalanceData
    ) {
        $this->_customerBalanceData = $customerBalanceData;
        $this->setCode('customerbalance');
    }

    /**
     * Collect customer balance totals for specified address
     *
     * @param Magento_Sales_Model_Quote_Address $address
     * @return Enterprise_CustomerBalance_Model_Total_Quote_Customerbalance
     */
    public function collect(Magento_Sales_Model_Quote_Address $address)
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
                $store = Mage::app()->getStore($quote->getStoreId());
                $baseBalance = Mage::getModel('Enterprise_CustomerBalance_Model_Balance')
                    ->setCustomer($quote->getCustomer())
                    ->setCustomerId($quote->getCustomer()->getId())
                    ->setWebsiteId($store->getWebsiteId())
                    ->loadByCustomer()
                    ->getAmount();
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

            $address->setBaseGrandTotal($address->getBaseGrandTotal()-$baseAmountLeft);
            $address->setGrandTotal($address->getGrandTotal()-$amountLeft);
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
     * @param Magento_Sales_Model_Quote_Address $address
     * @return Enterprise_CustomerBalance_Model_Total_Quote_Customerbalance
     */
    public function fetch(Magento_Sales_Model_Quote_Address $address)
    {
        if (!$this->_customerBalanceData->isEnabled()) {
            return $this;
        }
        if ($address->getCustomerBalanceAmount()) {
            $address->addTotal(array(
                'code'=>$this->getCode(),
                'title'=>__('Store Credit'),
                'value'=>-$address->getCustomerBalanceAmount(),
            ));
        }
        return $this;
    }
}
