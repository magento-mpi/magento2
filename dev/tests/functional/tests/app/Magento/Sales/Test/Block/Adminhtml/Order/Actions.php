<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Actions
 * Order actions block
 *
 * @package Magento\Sales\Test\Block\Adminhtml\Order
 */
class Actions extends Block
{
    /**
     * 'Back' button
     *
     * @var string
     */
    protected $back = '#back';

    /**
     * 'Edit' button
     *
     * @var string
     */
    protected $edit = '#order_edit';

    /**
     * 'Cancel' button
     *
     * @var string
     */
    protected $cancel = '#order_cancel';

    /**
     * 'Send Email' button
     *
     * @var string
     */
    protected $sendEmail = '#send_notification';

    /**
     * 'Void' button
     *
     * @var string
     */
    protected $void = '#void_payment';

    /**
     * 'Hold' button
     *
     * @var string
     */
    protected $hold = '#order_hold';

    /**
     * 'Invoice' button
     *
     * @var string
     */
    protected $invoice = '#order_invoice';

    /**
     * 'Ship' button
     *
     * @var string
     */
    protected $ship = '#order_ship';

    /**
     * 'Credit Memo' button
     *
     * @var string
     */
    protected $creditMemo = '#capture';

    /**
     * 'Refund' button
     *
     * @var string
     */
    protected $refund = '.submit-button.refund';

    /**
     * 'Refund Offline' button
     *
     * @var string
     */
    protected $refundOffline = '.submit-button';

    /**
     * Ship order
     */
    public function ship()
    {
        $this->_rootElement->find($this->ship)->click();
    }

    /**
     * Invoice order
     */
    public function invoice()
    {
        $this->_rootElement->find($this->invoice)->click();
    }

    /**
     * Go back
     */
    public function back()
    {
        $this->_rootElement->find($this->back)->click();
    }

    /**
     * Edit order
     */
    public function edit()
    {
        $this->_rootElement->find($this->edit)->click();
    }

    /**
     * Cancel order
     */
    public function cancel()
    {
        $this->_rootElement->find($this->cancel)->click();
    }

    /**
     * Send email
     */
    public function sendEmail()
    {
        $this->_rootElement->find($this->sendEmail)->click();
    }

    /**
     * Void order
     */
    public function void()
    {
        $this->_rootElement->find($this->void)->click();
    }

    /**
     * Hold order
     */
    public function hold()
    {
        $this->_rootElement->find($this->hold)->click();
    }

    /**
     * Credit memo
     */
    public function creditMemo()
    {
        $this->_rootElement->find($this->creditMemo)->click();
    }

    /**
     * Refund order
     */
    public function refund()
    {
        $this->_rootElement->find($this->refund, Locator::SELECTOR_CSS)->click();
    }

    /**
     * Refund offline order
     */
    public function refundOffline()
    {
        $this->_rootElement->find($this->refundOffline, Locator::SELECTOR_CSS)->click();
    }
}
