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
 * @package    Mage_Protx
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Protx Form Method Front Controller
 *
 * @category   Mage
 * @package    Mage_Protx
 * @name       Mage_Protx_StandardController
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Protx_StandardController extends Mage_Core_Controller_Front_Action
{
    /**
     * Get singleton with protx strandard
     *
     * @return object Mage_Protx_Model_Standard
     */
    public function getStandard()
    {
        return Mage::getSingleton('protx/standard');
    }

    /**
     * Get Config model
     *
     * @return object Mage_Protx_Model_Config
     */
    public function getConfig()
    {
        return $this->getStandard()->getConfig();
    }

    /**
     *  Return debug flag
     *
     *  @return  boolean
     */
    public function getDebug ()
    {
        return $this->getStandard()->getDebug();
    }

    /**
     * When a customer chooses Protx on Checkout/Payment page
     *
     */
    public function redirectAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setProtxStandardQuoteId($session->getQuoteId());

        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($session->getLastRealOrderId());
        $order->addStatusToHistory(
            $order->getStatus(),
            Mage::helper('protx')->__('Customer was redirected to Protx')
        );
        $order->save();

        $this->getResponse()
            ->setBody($this->getLayout()
                ->createBlock('protx/standard_redirect')
                ->setOrder($order)
                ->toHtml());

        $session->unsQuoteId();
    }

    /**
     *  Success response from Protx
     *
     *  @return	  void
     */
    public function  successResponseAction()
    {
        $this->preResponse();

        if (!$this->isValidResponse) {
            $this->_redirect('');
            return ;
        }

        $transactionId = $this->responseArr['VendorTxCode'];

        if ($this->getDebug()) {
            Mage::getModel('protx/api_debug')
                ->setResponseBody(print_r($this->responseArr,1))
                ->save();
        }

        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($transactionId);

        if (!$order->getId()) {
            /*
            * need to have logic when there is no order with the order id from protx
            */
            return false;
        }

        $order->addStatusToHistory(
            $order->getStatus(),
            Mage::helper('protx')->__('Customer successfully returned from Protx')
        );

        $order->sendNewOrderEmail();

        if (sprintf('%.2f', $this->responseArr['Amount']) != sprintf('%.2f', $order->getBaseGrandTotal())) {
            // cancel order
            $order->cancel();
            $order->addStatusToHistory(
                $order->getStatus(),
                Mage::helper('protx')->__('Order total amount does not match protx gross total amount')
            );
        } else {
            $order->getPayment()->setTransactionId($this->responseArr['VPSTxId']);

            if ($this->getConfig()->getPaymentType() == Mage_Protx_Model_Config::PAYMENT_TYPE_PAYMENT) {
                if ($this->saveInvoice($order)) {
                    $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
                } else {
                    $newOrderStatus = $this->getConfig()->getNewOrderStatus() ?
                        $this->getConfig()->getNewOrderStatus() : Mage_Sales_Model_Order::STATE_NEW;
                }
            } else {
                $order->addStatusToHistory(
                    $order->getStatus(),
                    Mage::helper('protx')->__($this->responseArr['StatusDetail'])
                );
            }
        }

        $order->save();

        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getProtxStandardQuoteId(true));
        Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
        $this->_redirect('checkout/onepage/success');
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
     *  Failure response from Protx
     *
     *  @return	  void
     */
    public function failureResponseAction ()
    {
        $this->preResponse();

        if (!$this->isValidResponse) {
            $this->_redirect('');
            return ;
        }

        $transactionId = $this->responseArr['VendorTxCode'];

        if ($this->getDebug()) {
            Mage::getModel('protx/api_debug')
                ->setResponseBody(print_r($this->responseArr,1))
                ->save();
        }

        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($transactionId);

        if (!$order->getId()) {
            /**
             * need to have logic when there is no order with the order id from protx
             */
            return false;
        }

        // cancel order in anyway
        $order->cancel();

        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getProtxStandardQuoteId(true));

        // Customer clicked CANCEL Butoon
        if ($this->responseArr['Status'] == 'ABORT') {
            $history = Mage::helper('protx')->__('Order '.$order->getId().' was canceled by customer');
            $redirectTo = 'checkout/cart';
        } else {
            $history = Mage::helper('protx')->__($this->responseArr['StatusDetail']);
            $session->setErrorMessage($this->responseArr['StatusDetail']);
            $redirectTo = 'protx/standard/failure';
        }

        $history = Mage::helper('protx')->__('Customer was returned from Protx.') . ' ' . $history;
        $order->addStatusToHistory($order->getStatus(), $history);
        $order->save();

        $this->_redirect($redirectTo);
    }

    /**
     *  Expected GET HTTP Method
     *
     *  @return	  void
     */
    protected function preResponse ()
    {
        $responseCryptString = $this->getRequest()->crypt;

        if ($responseCryptString != '') {
            $rArr = $this->getStandard()->cryptToArray($responseCryptString);
            $ok = is_array($rArr)
                && isset($rArr['Status']) && $rArr['Status'] != ''
                && isset($rArr['VendorTxCode']) && $rArr['VendorTxCode'] != ''
                && isset($rArr['Amount']) && $rArr['Amount'] != '';

            if ($ok) {
                $this->responseArr = $rArr;
                $this->isValidResponse = true;
            }
        }
    }

    /**
     *  Failure Action
     *
     *  @return	  void
     */
    public function failureAction ()
    {
        $session = Mage::getSingleton('checkout/session');

        if (!$session->getErrorMessage()) {
            $this->_redirect('checkout/cart');
            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('protx/session');
        $this->renderLayout();
    }
}