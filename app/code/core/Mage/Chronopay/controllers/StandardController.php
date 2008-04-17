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
 * @author     Dmitriy Volik <dmitriy.volik@varien.com>
 */
class Mage_Chronopay_StandardController extends Mage_Core_Controller_Front_Action
{
    /**
     * Crypt field contents returned from Chronopay
     */
    protected $responseArr = array();

    /**
     * Valid Response Indicator from Chronopay
     */
    protected $isValidResponse = false;

    /**
     * Order
     */
    protected $_order;

    /**
     * Get singleton with chronopay strandard
     *
     * @return object Mage_Chronopay_Model_Standard
     */
    public function getStandard()
    {
        return Mage::getSingleton('chronopay/standard');
    }

    /**
     * Get Config model
     *
     * @return object Mage_Chronopay_Model_Config
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
     *  Get order
     *
     *  @param    none
     *  @return	  object order
     */
    protected function getOrder ()
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

        $this->getResponse()->setBody($this->getLayout()->createBlock('chronopay/standard_redirect')->toHtml());

        $session = Mage::getSingleton('checkout/session');
        $session->setChronopayStandardQuoteId($session->getQuoteId());
        $session->unsQuoteId();
    }

    /**
     *  Example of Response data
     *
        [transaction_type] => onetime
        [customer_id] => 003725-000000676
        [site_id] => 003725-0056
        [product_id] => 003725-0056-0001
        [date] =>
        [time] =>
        [transaction_id] =>
        [email] => dmitriy.volik@varien.com
        [country] => ATA
        [name] => DMITRIY VOLIK
        [city] => Kyiv
        [street] => Kyiv333
        [phone] => 321-54-87
        [state] => XX
        [zip] => 1321366
        [language] => EN
        [cs1] =>
        [cs2] =>
        [cs3] =>
        [username] =>
        [password] =>
        [total] =>
        [currency] => USD
        [payment_type] => VISA / Visa Electron
        [sign] => 4797d777ed430eae032b7c1a49d42ff7     *
     */


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

        $this->_redirect('checkout/onepage/success');
    }


    /**
     *  Description goes here...
     *
     *  @param    none
     *  @return	  void
     */
    public function notifyAction ()
    {
        $this->preResponse();

        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($this->responseArr['cs1']);

        try {
            if (!$this->isValidResponse) {
                throw new Exception(Mage::helper('chronopay')->__('Invalid ChronoPay Response'));
            }

            if ($this->getDebug()) {
                Mage::getModel('chronopay/api_debug')
                    ->setResponseBody(print_r($this->responseArr,1))
                    ->save();
            }

            // validate order ID
            if (!$order->getId() || $order->getId() != $this->responseArr['cs1']) {
                throw new Exception(Mage::helper('chronopay')->__('Cannot restore order or invalid order ID'));
            }

            // validate site ID
            if ($this->getConfig()->getSiteId() != $this->responseArr['site_id']) {
                throw new Exception(Mage::helper('chronopay')->__('Invalid site ID'));
            }

            // validate product ID
            if ($this->getConfig()->getProductId() != $this->responseArr['product_id']) {
                throw new Exception(Mage::helper('chronopay')->__('Invalid product ID'));
            }

            // Successful transaction type
            if (!in_array($this->responseArr['transaction_type'], array('initial', 'onetime'))) {
                throw new Exception(Mage::helper('chronopay')->__('Transaction is not successful'));
            }

            $order->sendNewOrderEmail();

            if (sprintf('%.2f', $this->responseArr['total']) != sprintf('%.2f', $order->getBaseGrandTotal())) {
    //            $order->cancel();
                $order->addStatusToHistory(
                    $order->getStatus(),
                    Mage::helper('chronopay')->__('Order total amount does not match chronopay gross total amount')
                );
            }
            $order->getPayment()->setTransactionId($this->responseArr['transaction_id']);

            if ($this->saveInvoice($order)) {
                $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
                $order->addStatusToHistory(
                    $order->getStatus(),
                    Mage::helper('chronopay')->__('Invoice was created for order ' . $order->getId())
                );
            } else {
                $order->addStatusToHistory(
                    $this->getConfig()->getNewOrderStatus(),
                    Mage::helper('chronopay')->__('Cannot save invoice for order '.$order->getId())
                );
            }

            $order->save();

        } catch (Exception $e) {
            if ($order->getId()) {
                $order->addStatusToHistory(
                    $order->getStatus(),
                    $e->getMessage()
                );
                $order->cancel();
                $order->save();
            }
//            logme($e->getMessage());
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
//        $this->preResponse();
//
//        if (!$this->isValidResponse) {
//            $this->_redirect('');
//            return ;
//        }
//
//        $transactionId = $this->responseArr['VendorTxCode'];
//
//        if ($this->getDebug()) {
//            Mage::getModel('chronopay/api_debug')
//                ->setResponseBody(print_r($this->responseArr,1))
//                ->save();
//        }
//
//        $order = Mage::getModel('sales/order');
//        $order->loadByIncrementId($transactionId);
//
//        if (!$order->getId()) {
//            /**
//             * need to have logic when there is no order with the order id from chronopay
//             */
//            return false;
//        }
//
//        // cancel order in anyway
//        $order->cancel();
//
//        $session = Mage::getSingleton('checkout/session');
//        $session->setQuoteId($session->getChronopayStandardQuoteId(true));
//
//        // Customer clicked CANCEL Butoon
//        if ($this->responseArr['Status'] == 'ABORT') {
//            $history = Mage::helper('chronopay')->__('Order '.$order->getId().' was canceled by customer');
//            $redirectTo = 'checkout/cart';
//        } else {
//            $history = Mage::helper('chronopay')->__($this->responseArr['StatusDetail']);
//            $session->setErrorMessage($this->responseArr['StatusDetail']);
//            $redirectTo = 'chronopay/standard/failure';
//        }
//
//        $history = Mage::helper('chronopay')->__('Customer was returned from Chronopay.') . ' ' . $history;
//        $order->addStatusToHistory($order->getStatus(), $history);
//        $order->save();
//
//        $this->_redirect($redirectTo);
    }

    /**
     *  Expected POST Response from ChronoPay
     *
     *  @return	  void
     */
    protected function preResponse ()
    {
        $rArr = $this->getRequest()->getPost();

        if ($this->getDebug()) {
            Mage::getModel('chronopay/api_debug')
                ->setResponseBody(print_r($rArr,1))
                ->save();
        }

        $ok = is_array($rArr)
            && isset($rArr['transaction_type']) && $rArr['transaction_type'] != ''
            && isset($rArr['customer_id']) && $rArr['customer_id'] != ''
            && isset($rArr['site_id']) && $rArr['site_id'] != ''
            && isset($rArr['product_id']) && $rArr['product_id'] != '';

        if ($ok) {
            $this->responseArr = $rArr;
            $this->isValidResponse = true;
        }
    }
}