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
 * eWAY Shared Checkout Controller
 *
 * @category   Mage
 * @package    Mage_Eway
 * @author     Ruslan Voitenko <ruslan.voytenko@varien.com>
 */
class Mage_Eway_SharedController extends Mage_Core_Controller_Front_Action
{
    protected function _expireAjax()
    {
        if (!Mage::getSingleton('checkout/session')->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1','403 Session Expired');
            exit;
        }
    }

    /**
     * Get singleton of Shared Model
     * 
     * @return Mage_Eway_Model_Shared
     */
    public function getModel()
    {
        return Mage::getSingleton('eway/shared');
    }
    
    /**
     * Get singleton of Checkout Session Model
     * 
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * when customer select eWay Shared payment method
     */
    public function redirectAction()
    {
        $session = $this->getCheckout();

        $this->getModel()->setCheckout($session);
        $this->getModel()->setQuote($session->getQuote());

        $session->setEwayQuoteId($session->getQuoteId());
        $session->setEwayRealOrderId($session->getLastRealOrderId());
        
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($session->getLastRealOrderId());
        $order->addStatusToHistory($order->getStatusLabel(), Mage::helper('eway')->__('Customer was redirected to eWAY.'));
        $order->save();

        $this->getResponse()->setBody($this->getLayout()->createBlock('eway/shared_redirect')->toHtml());

        $session->unsQuoteId();
    }

    /**
     * eWay returns POST variables to this action
     */
    public function successAction()
    {
        $this->_checkReturnedPost();

        $session = $this->getCheckout();

        $session->unsEwayRealOrderId();
        $session->setQuoteId($session->getEwayQuoteId(true));
        $session->getQuote()->setIsActive(false)->save();

        $order = Mage::getModel('sales/order');
        $order->load($session->getLastOrderId());
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
                $order->addStatusToHistory($order->getStatusLabel(), Mage::helper('eway')->__('Customer successfuly returned from eWAY'));
            }
        } else {
            $order->cancel();
            $order->addStatusToHistory($order->getStatusLabel(), Mage::helper('eway')->__('Customer was rejected by eWAY'));
        }

        $order->save();

        return;
    }

}
