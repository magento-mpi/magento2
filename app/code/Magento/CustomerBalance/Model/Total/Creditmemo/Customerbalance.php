<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_CustomerBalance_Model_Total_Creditmemo_Customerbalance extends Magento_Sales_Model_Order_Creditmemo_Total_Abstract
{
    /**
     * Customer balance data
     *
     * @var Magento_CustomerBalance_Helper_Data
     */
    protected $_customerBalanceData = null;

    /**
     * @param Magento_CustomerBalance_Helper_Data $customerBalanceData
     * @param array $data
     */
    public function __construct(
        Magento_CustomerBalance_Helper_Data $customerBalanceData,
        array $data = array()
    ) {
        $this->_customerBalanceData = $customerBalanceData;
        parent::__construct($data);
    }

    /**
     * Collect customer balance totals for credit memo
     *
     * @param Magento_Sales_Model_Order_Creditmemo $creditmemo
     * @return Magento_CustomerBalance_Model_Total_Creditmemo_Customerbalance
     */
    public function collect(Magento_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $creditmemo->setBsCustomerBalTotalRefunded(0);
        $creditmemo->setCustomerBalTotalRefunded(0);

        $creditmemo->setBaseCustomerBalanceReturnMax(0);
        $creditmemo->setCustomerBalanceReturnMax(0);

        if (!$this->_customerBalanceData->isEnabled()) {
            return $this;
        }

        $order = $creditmemo->getOrder();
        if ($order->getBaseCustomerBalanceAmount() && $order->getBaseCustomerBalanceInvoiced() != 0) {
            $cbLeft = $order->getBaseCustomerBalanceInvoiced() - $order->getBaseCustomerBalanceRefunded();

            $used = 0;
            $baseUsed = 0;

            if ($cbLeft >= $creditmemo->getBaseGrandTotal()) {
                $baseUsed = $creditmemo->getBaseGrandTotal();
                $used = $creditmemo->getGrandTotal();

                $creditmemo->setBaseGrandTotal(0);
                $creditmemo->setGrandTotal(0);

                $creditmemo->setAllowZeroGrandTotal(true);
            } else {
                $baseUsed = $order->getBaseCustomerBalanceInvoiced() - $order->getBaseCustomerBalanceRefunded();
                $used = $order->getCustomerBalanceInvoiced() - $order->getCustomerBalanceRefunded();

                $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal()-$baseUsed);
                $creditmemo->setGrandTotal($creditmemo->getGrandTotal()-$used);
            }

            $creditmemo->setBaseCustomerBalanceAmount($baseUsed);
            $creditmemo->setCustomerBalanceAmount($used);
        }

        $creditmemo->setBaseCustomerBalanceReturnMax($creditmemo->getBaseCustomerBalanceReturnMax() + $creditmemo->getBaseGrandTotal());
        $creditmemo->setBaseCustomerBalanceReturnMax($creditmemo->getBaseCustomerBalanceReturnMax() + $creditmemo->getBaseCustomerBalanceAmount());

        $creditmemo->setCustomerBalanceReturnMax($creditmemo->getCustomerBalanceReturnMax() + $creditmemo->getCustomerBalanceAmount());

        return $this;
    }
}
