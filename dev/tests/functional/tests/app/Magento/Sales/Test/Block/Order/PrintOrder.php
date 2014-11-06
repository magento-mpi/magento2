<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Order;

use Mtf\Block\Block;

/**
 * Print Order block.
 */
class PrintOrder extends Block
{
    /**
     * Billing address selector.
     *
     * @var string
     */
    protected $billingAddressSelector = '.billing';

    /**
     * Totals selector.
     *
     * @var string
     */
    protected $totalsSelector = '.order tfoot';

    /**
     * Items selector.
     *
     * @var string
     */
    protected $itemsSelector = '.items';

    /**
     * Payment method selector.
     *
     * @var string
     */
    protected $paymentMethodSelector = '.payment-method';

    /**
     * Returns billing address block on print order page.
     *
     * @return \Magento\Sales\Test\Block\Order\PrintOrder\BillingAddress
     */
    public function getBillingAddressBlock()
    {
        return $this->blockFactory->create(
            'Magento\Sales\Test\Block\Order\PrintOrder\BillingAddress',
            ['element' => $this->_rootElement->find($this->billingAddressSelector)]
        );
    }

    /**
     * Returns totals block on print order page.
     *
     * @return \Magento\Sales\Test\Block\Order\PrintOrder\Totals
     */
    public function getTotalsBlock()
    {
        return $this->blockFactory->create(
            'Magento\Sales\Test\Block\Order\PrintOrder\Totals',
            ['element' => $this->_rootElement->find($this->totalsSelector)]
        );
    }

    /**
     * Returns items block on print order page.
     *
     * @return \Magento\Sales\Test\Block\Order\PrintOrder\Items
     */
    public function getItemsBlock()
    {
        return $this->blockFactory->create(
            'Magento\Sales\Test\Block\Order\PrintOrder\Items',
            ['element' => $this->_rootElement->find($this->itemsSelector)]
        );
    }

    /**
     * Returns payment method block on print order page.
     *
     * @return \Magento\Sales\Test\Block\Order\PrintOrder\PaymentMethod
     */
    public function getPaymentMethodBlock()
    {
        return $this->blockFactory->create(
            'Magento\Sales\Test\Block\Order\PrintOrder\PaymentMethod',
            ['element' => $this->_rootElement->find($this->paymentMethodSelector)]
        );
    }
}
