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
 * iDEAL Basic Checkout Controller
 *
 * @category    Mage
 * @package     Mage_Ideal
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Ideal_BasicController extends Mage_Core_Controller_Front_Action
{
    /**
     * Get singleton with ideal strandard
     *
     * @return object Mage_Ideal_Model_Basic
     */
    public function getBasic()
    {
        return Mage::getSingleton('ideal/basic');
    }

    /**
     *  Return order instance for last real order ID (stored in session)
     *
     *  @param    none
     *  @return	  Mage_Sales_Model_Entity_Order object
     */
    protected function getOrder ()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setIdealBasicQuoteId($session->getQuoteId());

        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($session->getLastRealOrderId());
        return $order;
    }

    /**
     * When a customer chooses iDEAL Basic on Checkout/Payment page
     */
    public function redirectAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setIdealBasicQuoteId($session->getQuoteId());
        $order = $this->getOrder();
        $order->addStatusToHistory(
            $order->getStatus(),
            Mage::helper('ideal')->__('Customer was redirected to iDeal')
        );
        $order->save();

        $this->getResponse()->setBody($this->getLayout()->createBlock('ideal/basic_redirect')->toHtml());
        $session->unsQuoteId();
    }

    /**
     *  Perform some validate actions for iDeal Response
     *
     *  @return	  void
     */
    protected function preResponse ()
    {
        if ($this->getBasic()->getDebug()) {
            Mage::getModel('ideal/api_debug')
                ->setResponseBody($_SERVER['HTTP_REFERER']) // :(
                ->save();
        }
    }

    /**
     *  Success response from iDeal
     *
     *  @return	  void
     */
    public function  successAction()
    {
        $order = $this->getOrder();

        if (!$order->getId()) {
            $this->_redirect('');
            return false;
        }

        $order->addStatusToHistory(
            $order->getStatus(),
            Mage::helper('ideal')->__('Customer successfully returned from iDeal')
        );

        $order->sendNewOrderEmail();

        if ($this->saveInvoice($order)) {
            $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
            $order->addStatusToHistory(
                $order->getStatus(),
                Mage::helper('ideal')->__('Invoice was created for order ' . $order->getId())
            );
        } else {
            $order->addStatusToHistory(
                Mage::getStoreConfig('payment/ideal_basic/order_status'),
                Mage::helper('ideal')->__('Cannot save invoice for order '.$order->getId())
            );
        }

        $order->addStatusToHistory(
            $order->getStatus(),
            Mage::helper('ideal')->__('Please, check the status of a transaction via the '
                                      . 'ING iDEAL Dashboard before delivery of the goods purchased.')
        );

        $order->save();

        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getIdealBasicQuoteId(true));
        $session->getQuote()->setIsActive(false)->save();
        $this->_redirect('checkout/onepage/success');
    }

    /**
     *  Cancel response from iDeal
     *
     *  @return	  void
     */
    public function cancelAction()
    {
        $order = $this->getOrder();

        if (!$order->getId()) {
            $this->_redirect('');
            return false;
        }

        $order->cancel();

        $history = Mage::helper('ideal')->__('Order '.$order->getId().' was canceled by customer')
                 . Mage::helper('ideal')->__('Customer was returned from iDeal.');

        $order->addStatusToHistory(
            $order->getStatus(),
            $history
        );

        $order->save();

        $this->_redirect('checkout/cart');
    }


    /**
     *  Error response from iDeal
     *
     *  @return	  void
     */
    public function errorAction ()
    {
        $order = $this->getOrder();

        if (!$order->getId()) {
            $this->_redirect('');
            return false;
        }

        $order->cancel();

        $history = Mage::helper('ideal')->__('Error was occured with order '.$order->getId())
                 . Mage::helper('ideal')->__('Customer was returned from iDeal.');

        $order->addStatusToHistory(
            $order->getStatus(),
            $history
        );

        $order->save();

        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getIdealBasicQuoteId(true));
        $session->setErrorMessage(Mage::helper('ideal')->__('Error was occured with order '.$order->getId()));
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


}
