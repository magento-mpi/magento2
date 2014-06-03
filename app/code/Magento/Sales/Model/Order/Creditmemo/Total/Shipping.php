<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Order\Creditmemo\Total;

/**
 * Order creditmemo shipping total calculation model
 */
class Shipping extends AbstractTotal
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Tax\Model\Config
     */
    protected $_taxConfig;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Tax\Model\Config $taxConfig,
        array $data = array()
    ) {
        parent::__construct($data);
        $this->_storeManager = $storeManager;
        $this->_taxConfig = $taxConfig;
    }

    /**
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return $this
     * @throws \Magento\Framework\Model\Exception
     */
    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        $order = $creditmemo->getOrder();
        $allowedAmount = $order->getShippingAmount() - $order->getShippingRefunded();
        $baseAllowedAmount = $order->getBaseShippingAmount() - $order->getBaseShippingRefunded();

        $shipping = $order->getShippingAmount();
        $baseShipping = $order->getBaseShippingAmount();
        $shippingInclTax = $order->getShippingInclTax();
        $baseShippingInclTax = $order->getBaseShippingInclTax();

        $isShippingInclTax = $this->_taxConfig->displaySalesShippingInclTax($order->getStoreId());

        /**
         * Check if shipping amount was specified (from invoice or another source).
         * Using has magic method to allow setting 0 as shipping amount.
         */
        if ($creditmemo->hasBaseShippingAmount()) {
            $baseShippingAmount = $this->_storeManager->getStore()->roundPrice($creditmemo->getBaseShippingAmount());
            if ($isShippingInclTax && $baseShippingInclTax != 0) {
                $part = $baseShippingAmount / $baseShippingInclTax;
                $shippingInclTax = $this->_storeManager->getStore()->roundPrice($shippingInclTax * $part);
                $baseShippingInclTax = $baseShippingAmount;
                $baseShippingAmount = $this->_storeManager->getStore()->roundPrice($baseShipping * $part);
            }
            /*
             * Rounded allowed shipping refund amount is the highest acceptable shipping refund amount.
             * Shipping refund amount shouldn't cause errors, if it doesn't exceed that limit.
             * Note: ($x < $y + 0.0001) means ($x <= $y) for floats
             */
            if ($baseShippingAmount < $this->_storeManager->getStore()->roundPrice($baseAllowedAmount) + 0.0001) {
                /*
                 * Shipping refund amount should be equated to allowed refund amount,
                 * if it exceeds that limit.
                 * Note: ($x > $y - 0.0001) means ($x >= $y) for floats
                 */
                if ($baseShippingAmount > $baseAllowedAmount - 0.0001) {
                    $shipping = $allowedAmount;
                    $baseShipping = $baseAllowedAmount;
                } else {
                    if ($baseShipping != 0) {
                        $shipping = $shipping * $baseShippingAmount / $baseShipping;
                    }
                    $shipping = $this->_storeManager->getStore()->roundPrice($shipping);
                    $baseShipping = $baseShippingAmount;
                }
            } else {
                $baseAllowedAmount = $order->getBaseCurrency()->format($baseAllowedAmount, null, false);
                throw new \Magento\Framework\Model\Exception(
                    __('Maximum shipping amount allowed to refund is: %1', $baseAllowedAmount)
                );
            }
        } else {
            if ($baseShipping != 0) {
                $allowedTaxAmount = $order->getShippingTaxAmount() - $order->getShippingTaxRefunded();
                $baseAllowedTaxAmount = $order->getBaseShippingTaxAmount() - $order->getBaseShippingTaxRefunded();

                $shippingInclTax = $this->_storeManager->getStore()->roundPrice($allowedAmount + $allowedTaxAmount);
                $baseShippingInclTax = $this->_storeManager->getStore()->roundPrice(
                    $baseAllowedAmount + $baseAllowedTaxAmount
                );
            }
            $shipping = $allowedAmount;
            $baseShipping = $baseAllowedAmount;
        }

        $creditmemo->setShippingAmount($shipping);
        $creditmemo->setBaseShippingAmount($baseShipping);
        $creditmemo->setShippingInclTax($shippingInclTax);
        $creditmemo->setBaseShippingInclTax($baseShippingInclTax);

        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $shipping);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseShipping);
        return $this;
    }
}
