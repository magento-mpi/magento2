<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales order edit controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Sales_Order_InvoiceController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Additional initialization
     *
     */
    protected function _construct()
    {
        $this->setUsedModuleName('Mage_Sales');
    }

    /**
     * Initialize current order model instance
     *
     * @return bool
     */
    protected function _initOrder()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($orderId);
        if ($order->getId()) {
            Mage::register('current_order', $order);
            return true;
        }
        return false;
    }

    protected function _initInvoice()
    {
        $invoiceId = $this->getRequest()->getParam('invoice_id');
        $invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
        if ($invoice->getId()) {
            Mage::register('current_invoice', $invoice);
            return true;
        }
        return false;
    }

    /**
     * Invoice create page
     */
    public function newAction()
    {
        if ($this->_initOrder()) {
            $this->loadLayout()
                ->_setActiveMenu('sales/order')
                ->_addContent($this->getLayout()->createBlock('adminhtml/sales_order_invoice_create'))
                ->renderLayout();
        }
        else {
            $this->_forward('noRoute');
        }
    }

    /**
     * Invoice edit page
     */
    public function editAction()
    {
        if ($this->_initInvoice()) {

        }
        else {
            $this->_forward('noRoute');
        }
    }

    /**
     * Save invoice
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost('invoice');
        /**
         * $data = array(
         *  'do_capture' => optional bool,
         *  'do_shipment'=> optional bool,
         *  'items' => array(
         *      $orderItemId || $invoiceItemId => $qtyToInvoice
         *  )
         * )
         */

        if ($this->_initOrder()) {
            $convertor = Mage::getModel('sales/convert_order');
            $order  = Mage::registry('current_order');
            $invoice= $convertor->toInvoice($order);
            $invoice->setPayment($convertor->paymentToInvoicePayment($order->getPayment()));

            if (isset($data['items'])) {
                foreach ($data['items'] as $orderItemId => $qtyToInvoice) {
                    $item = $convertor->itemToInvoiceItem($order->getItemById($orderItemId));
                    $item->setQty($qtyToInvoice);
                	$invoice->addItem($item);
                }
            }
        }
        elseif ($this->_initInvoice()) {
            $invoice = Mage::registry('current_invoice');
        }
        else {
            $this->_forward('noRoute');
            return;
        }

        try {
            if (!empty($data['do_capture'])) {
                $invoice->capture();
            }
            //$invoice->save();
        }
        catch (Mage_Core_Exception $e) {

        }
        catch (Exception $e) {

        }
    }

    public function captureAction()
    {

    }
}