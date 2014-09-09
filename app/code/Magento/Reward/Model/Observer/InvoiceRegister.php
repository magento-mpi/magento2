<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Observer;

class InvoiceRegister
{
    /**
     * Set invoiced reward amount to order after invoice register
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /* @var $invoice \Magento\Sales\Model\Order\Invoice */
        $invoice = $observer->getEvent()->getInvoice();
        if ($invoice->getBaseRewardCurrencyAmount()) {
            $order = $invoice->getOrder();
            $order->setRwrdCurrencyAmountInvoiced(
                $order->getRwrdCurrencyAmountInvoiced() + $invoice->getRewardCurrencyAmount()
            );
            $order->setBaseRwrdCrrncyAmtInvoiced(
                $order->getBaseRwrdCrrncyAmtInvoiced() + $invoice->getBaseRewardCurrencyAmount()
            );
        }

        return $this;
    }
}
