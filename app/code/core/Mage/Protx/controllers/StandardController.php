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
 * @author     Dmitriy Volik <dmitriy.volik@varien.com>
 */
class Mage_Protx_StandardController extends Mage_Core_Controller_Front_Action
{
    /**
     * Crypt field contents returned from Protx
     */
    protected $responseArr = array();

    /**
     * Valid Response Indicator from Protx
     */
    protected $isValidResponse = false;

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
        $this->getResponse()->setBody($this->getLayout()->createBlock('protx/standard_redirect')->toHtml());
        $session->unsQuoteId();
    }

    /**
     *  Example Crypt field contents:
     *
     *   [Status] => OK
     *   [StatusDetail] => Successfully Authorised Transaction
     *   [VendorTxCode] => 100000061 (== orderId)
     *   [VPSTxId] => {CA8D1CC1-22E8-4F42-8FDD-BAF0F1A85C8B}
     *   [TxAuthNo] => 7349
     *   [Amount] => 463
     *   [AVSCV2] => ALL MATCH
     *   [AddressResult] => MATCHED
     *   [PostCodeResult] => MATCHED
     *   [CV2Result] => MATCHED
     *   [GiftAid] => 0
     *   [3DSecureStatus] => OK
     *   [CAVV] => MNAXJRSRZK22PYKXPCFG1Z
     *
     */


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

        $order->sendNewOrderEmail();

        if (sprintf('%.2f', $this->responseArr['Amount']) != sprintf('%.2f', $order->getGrandTotal())) {
            $order->addStatusToHistory(
                $order->getStatus(),
                Mage::helper('protx')->__('Order total amount does not match protx gross total amount')
            );
        } else {
            $order->getPayment()->setTransactionId($this->responseArr['VPSTxId']);
            if ($this->getConfig()->getPaymentType() == Mage_Protx_Model_Config::PAYMENT_TYPE_PAYMENT) {
                $this->saveInvoice($order);
            } else {
                $order->addStatusToHistory(
                    $this->getConfig()->getNewOrderStatus(), //update order status to processing after creating an invoice
                    Mage::helper('protx')->__('Order '.$order->getId().' has pending status')
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

            $order->addStatusToHistory(
                'processing',//update order status to processing after creating an invoice
                Mage::helper('protx')->__('Invoice '.$invoice->getIncrementId().' was created')
            );

            return true;

        } else {
            $order->addStatusToHistory(
                $order->getStatus(),
                Mage::helper('protx')->__('Error in creating an invoice')
            );

            return false;
        }
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

        $order->cancel()->save();

        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getProtxStandardQuoteId(true));

        // Customer clicked CANCEL Butoon
        if ($this->responseArr['Status'] == 'ABORT') {
            $this->_redirect('checkout/cart');
            return;
            /*$order->addStatusToHistory(
                Mage_Sales_Model_Order::STATE_CANCELED,
                Mage::helper('protx')->__('Order '.$order->getId().' was canceled by customer')
            );*/
        }

        $session->setErrorMessage($this->responseArr['StatusDetail']);
        $this->_redirect('protx/standard/failure');
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
        $this->loadLayout();
        $this->_initLayoutMessages('protx/session');
        $this->renderLayout();
    }
}