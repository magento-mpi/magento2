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

namespace Magento\Backend\Test\Block\Sales\Order;

use Mtf\Block\Block;

/**
 * Class Actions
 * Order actions block
 *
 * @package Magento\Backend\Test\Block\Sales\Order
 */
class Actions extends Block
{

    /**#@+
     * Button selector
     * @var string
     */
    private $back;
    private $edit;
    private $cancel;
    private $sendEmail;
    private $void;
    private $hold;
    private $invoice;
    private $ship;
    /**#@-*/

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        $this->back = '#back';
        $this->edit = '#order_edit';
        $this->cancel = '#order_cancel';
        $this->sendEmail = '#send_notification';
        $this->void = '#void_payment';
        $this->hold = '#order_hold';
        $this->invoice = '#order_invoice';
        $this->ship = '#order_ship';
    }

    /**
     * Ship order
     */
    public function ship()
    {
        $this->_rootElement->find($this->ship)->click();
    }

    /**
     * Ship order
     */
    public function invoice()
    {
        $this->_rootElement->find($this->invoice)->click();
    }
}
