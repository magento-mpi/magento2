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
 * @package    Mage_Ideal
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * iDEAL Advanced Checkout Controller
 *
 * @category    Mage
 * @package     Mage_Ideal
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Ideal_AdvancedController extends Mage_Core_Controller_Front_Action
{
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * When a customer chooses iDEAL Advanced on Checkout/Payment page
     *
     */
    public function redirectAction()
    {
        $order = Mage::getModel('sales/order');
        $order->load($this->getCheckout()->getLastOrderId());
        if($order->getId()){
            $advanced = $order->getPayment()->getMethodInstance();
            $issuerId = $order->getPayment()->getIdealIssuerId();

            $response = $advanced->sendTransactionRequest($order, $issuerId);

            if ($response) {
                $order->getPayment()->setTransactionId($response->getTransactionId());
                $order->getPayment()->setLastTransId($response->getTransactionId());
                $order->getPayment()->setIdealTransactionChecked(0);

                if ($response->getError()) {
                    $this->getCheckout()->setIdealErrorMessage($response->getError());
                    $this->_redirect('*/*/failure');
                    return;
                }

                $this->getResponse()->setBody(
                    $this->getLayout()->createBlock('ideal/advanced_redirect')
                        ->setMessage(Mage::helper('ideal')->__('You will be redirected to bank in a few seconds.'))
                        ->setRedirectUrl($response->getIssuerAuthenticationUrl())
                        ->toHtml()
                );

                $this->getCheckout()->setIdealAdvancedQuoteId($this->getCheckout()->getQuoteId());
                $this->getCheckout()->unsQuoteId();

                $order->addStatusToHistory(
                    $order->getStatus(),
                    Mage::helper('ideal')->__('Customer was redirected to iDEAL')
                );
                $order->save();
                return;
            }
        }

        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('ideal/advanced_redirect')
                ->setMessage(Mage::helper('ideal')->__('Error occured. You will be redirected back to store.'))
                ->setRedirectUrl(Mage::getUrl('checkout/cart'))
                ->toHtml()
        );
    }

    /**
     * When a customer cancels payment from iDEAL
     */
    public function cancelAction()
    {
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($this->getCheckout()->getLastRealOrderId());
        $order->addStatusToHistory(
            $order->getStatus(),
            Mage::helper('ideal')->__('Customer canceled payment.')
        );
        $order->cancel();
        $order->save();

        $$this->getCheckout()->setQuoteId($$this->getCheckout()->setIdealAdvancedQuoteId(true));
        $this->_redirect('checkout/cart');
    }

    /**
     * When customer return from iDEAL
     */
    public function  resultAction()
    {
        /**
         * Decrypt Real Order Id that was sent encrypted
         */
        $orderId = Mage::helper('core')->decrypt(base64_encode(pack('H*', $this->getRequest()->getParam('ec'))));
        $transactionId = $this->getRequest()->getParam('trxid');

        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($orderId);

        if ($order->getId() > 0) {
            $advanced = $order->getPayment()->getMethodInstance();
            $advanced->setTransactionId($transactionId);
            $response = $advanced->getTransactionStatus($transactionId);
            if ($response->getTransactionStatus() == Mage_Ideal_Model_Api_Advanced::STATUS_SUCCESS) {
                $this->getCheckout()->setQuoteId($this->getCheckout()->getIdealAdvancedQuoteId(true));
                $this->getCheckout()->unsIdealAdvancedQuoteId();

                $this->getCheckout()->getQuote()->setIsActive(false)->save();

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

                    $order->addStatusToHistory($order->getStatus(), Mage::helper('ideal')->__('Customer successfully returned from iDEAL'));
                }

                $order->sendNewOrderEmail();

                $this->_redirect('checkout/onepage/success');
            } else if ($response->getTransactionStatus() == Mage_Ideal_Model_Api_Advanced::STATUS_CANCELLED) {
                $this->getCheckout()->setQuoteId($this->getCheckout()->getIdealAdvancedQuoteId(true));
                $this->getCheckout()->unsIdealAdvancedQuoteId();

                $order->cancel();
                $order->addStatusToHistory($order->getStatus(), Mage::helper('ideal')->__('Customer cancelled payment'));

                $this->_redirect('checkout/cart');
            } else {
                $this->getCheckout()->setQuoteId($this->getCheckout()->getIdealAdvancedQuoteId(true));
                $this->getCheckout()->unsIdealAdvancedQuoteId();

                $order->cancel();
                $order->addStatusToHistory($order->getStatus(), Mage::helper('ideal')->__('Customer was rejected by iDEAL'));
                $this->getCheckout()->setIdealErrorMessage(
                    Mage::helper('ideal')->__('An error occurred while processing your iDEAL transaction. Please contact the web shop or try
again later. Transaction number is %s.', $order->getIncrementId())
                );

                $this->_redirect('*/*/failure');
            }
            $order->getPayment()->setIdealTransactionChecked(1);
            $order->save();
        } else {
            $this->_redirect('checkout/cart');
        }
    }

    /**
     * Redirected here when customer returns with error
     */
    public function failureAction()
    {
        if (!$this->getCheckout()->getIdealErrorMessage()) {
            $this->norouteAction();
            return;
        }

        $this->loadLayout();
        $this->renderLayout();
    }
}
