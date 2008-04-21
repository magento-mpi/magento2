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
 * @package    Mage_Chronopay
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Chronopay Standard Front Controller
 *
 * @category   Mage
 * @package    Mage_Chronopay
 * @name       Mage_Chronopay_StandardController
 */
class Mage_Chronopay_StandardController extends Mage_Core_Controller_Front_Action
{
    /**
     * Order instance
     */
    protected $_order;

    /**
     *  Return debug flag
     *
     *  @return  boolean
     */
    public function getDebug ()
    {
        return Mage::getSingleton('chronopay/config')->getDebug();
    }

    /**
     *  Get order
     *
     *  @param    none
     *  @return	  Mage_Sales_Model_Order
     */
    public function getOrder ()
    {
        if ($this->_order == null) {
            $session = Mage::getSingleton('checkout/session');
            $session->setChronopayStandardQuoteId($session->getQuoteId());

            $this->_order = Mage::getModel('sales/order');
            $this->_order->loadByIncrementId($session->getLastRealOrderId());
        }
        return $this->_order;
    }

    /**
     * When a customer chooses Chronopay on Checkout/Payment page
     *
     */
    public function redirectAction()
    {
        $order = $this->getOrder();

        $order->addStatusToHistory(
            $order->getStatus(),
            Mage::helper('chronopay')->__('Customer was redirected to Chronopay')
        );
        $order->save();

        $this->getResponse()
            ->setBody($this->getLayout()
                ->createBlock('chronopay/standard_redirect')
                ->setOrder($order)
                ->toHtml());

        $session = Mage::getSingleton('checkout/session');
        $session->setChronopayStandardQuoteId($session->getQuoteId());
        $session->unsQuoteId();
    }

    /**
     *  Success response from Chronopay
     *
     *  @return	  void
     */
    public function  successAction()
    {
        $order = $this->getOrder();
        $order->addStatusToHistory(
            $order->getStatus(),
            Mage::helper('chronopay')->__('Customer successfully returned from Chronopay')
        );


        $session = Mage::getSingleton('checkout/session');
        $session->setSuccessMessage(Mage::helper('chronopay')->__('Your Order #') . $order->getRealOrderId());

        $order->save();

        $this->loadLayout();
        $this->_initLayoutMessages('checkout/session');
        $this->renderLayout();
    }


    /**
     *  Notification Action from ChronoPay
     *
     *  @param    none
     *  @return	  void
     */
    public function notifyAction ()
    {
        $postData = $this->getRequest()->getPost();
        if ($this->getDebug()) {
            Mage::getModel('chronopay/api_debug')
                ->setResponseBody(print_r($postData,1))
                ->save();
        }

        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId(Mage::helper('core')->decrypt($postData['cs1']));

        $result = $order->getPayment()->getMethodInstance()->setOrder($order)->validateResponse($postData);

        if ($result instanceof Exception) {
            if ($order->getId()) {
                $order->addStatusToHistory(
                    $order->getStatus(),
                    Mage::helper('chronopay')->__($result->getMessage())
                );
                $order->cancel();
                $order->save();
            }
            Mage::throwException(Mage::helper('chronopay')->__($result->getMessage()));
            return;
        }

        $order->sendNewOrderEmail();

        $order->getPayment()->getMethodInstance()->setTransactionId($postData['transaction_id']);

        if ($this->saveInvoice($order)) {
            $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
            $order->save();
        }
    }

    /**
     *  Save invoice for order
     *
     *  @param    Mage_Sales_Model_Order $order
     *  @return	  boolean Can save invoice or not
     */
    protected function saveInvoice (Mage_Sales_Model_Order $order)
    {
        if ($order->canInvoice()) {
            $convertor = Mage::getModel('sales/convert_order');
            $invoice = $convertor->toInvoice($order);
            foreach ($order->getAllItems() as $orderItem) {
               if (!$orderItem->getQtyToInvoice()) {
                   continue;
               }
               $item = $convertor->itemToInvoiceItem($orderItem);
               $item->setQty($orderItem->getQtyToInvoice());
               $invoice->addItem($item);
            }
            $invoice->collectTotals();
            $invoice->register()->capture();
            Mage::getModel('core/resource_transaction')
               ->addObject($invoice)
               ->addObject($invoice->getOrder())
               ->save();
            return true;
        }

        return false;
    }

    /**
     *  Failure response from Chronopay
     *
     *  @return	  void
     */
    public function failureAction ()
    {
        $errorMsg = Mage::helper('chronopay')->__('There was an error occurred during paying process.');

        $order = $this->getOrder();

        if ($order instanceof Mage_Sales_Model_Order && $order->getId()) {
            $order->addStatusToHistory($order->getStatus(), $errorMsg);
            $order->cancel();
            $order->save();
        }
        $this->loadLayout();
        $this->renderLayout();
    }

}