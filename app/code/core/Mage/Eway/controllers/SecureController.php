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
 * @package    Mage_Eway
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Eway 3D-Secure Checkout Controller
 */
class Mage_Eway_SecureController extends Mage_Core_Controller_Front_Action
{
    protected function _expireAjax()
    {
        if (!$this->getCheckout()->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1','403 Session Expired');
            exit;
        }
    }

    /**
     * @return Mage_Eway_Model_Secure
     */
    public function getModel()
    {
        return Mage::getSingleton('eway/secure');
    }
    
    /**
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * when customer select eWay 3D-Secure payment method
     */
    public function redirectAction()
    {
        $session = $this->getCheckout();
        
        $this->getModel()->setCheckout($session);
        $this->getModel()->setQuote($session->getQuote());

        $session->setEwayQuoteId($session->getQuoteId());
        $session->setEwayRealOrderId($session->getLastRealOrderId());
        
        $this->getResponse()->setBody($this->getLayout()->createBlock('eway/secure_redirect')->toHtml());
        
        $session->unsQuoteId();
    }

    /**
     * eWay returns POST variables to this action
     */
    public function  successAction()
    {
        $this->_checkReturnedPost();
        
        $session = $this->getCheckout();
        
        $session->unsEwayRealOrderId();
        $session->setQuoteId($session->getEwayQuoteId(true));
        $session->getQuote()->setIsActive(false)->save();
        
        $order = Mage::getModel('sales/order');
        $order->load($this->getCheckout()->getLastOrderId());
        if($order->getId()) {
            $order->sendNewOrderEmail();
        }

        $this->_redirect('checkout/onepage/success');
    }

    /**
     * Checking POST variables.
     * Creating invoice if payment was successfull or cancel order if payment was declined
     */
    protected function _checkReturnedPost()
    {
        if (!$this->getRequest()->isPost()) {
            $this->_redirect('');
            return;
        }
        
        $response = $this->getRequest()->getPost();

        if ($this->getCheckout()->getEwayRealOrderId() != $response['ewayTrxnNumber']) {
            $this->_redirect('');
            return;
        }
        
        $this->getModel()->setResponse($response);
        
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($response['ewayTrxnNumber']);

        if ($this->getModel()->parseResponse()) {

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
                    
                $this->getModel()->setTransactionId($response['ewayTrxnReference']);
            }
        } else {
            $order->cancel();
        }

        $order->save();

        return;
}

}