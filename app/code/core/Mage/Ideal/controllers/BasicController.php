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
     *  Return order instance for last real order ID (stored in session)
     *
     *  @param    none
     *  @return	  Mage_Sales_Model_Entity_Order object
     */
    protected function getOrder ()
    {
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId(Mage::getSingleton('checkout/session')->getLastRealOrderId());
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
            Mage::helper('ideal')->__('Customer was redirected to iDEAL.' .
                ' Please, check the status of a transaction via the ' .
                'ING iDEAL Dashboard before delivery of the goods purchased.')
        );
        $order->save();

        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('ideal/basic_redirect')
                ->setOrder($order)
                ->toHtml()
            );
        $session->unsQuoteId();
    }

    /**
     *  Success response from iDEAL
     *
     *  @return	  void
     */
    public function  successAction()
    {
        $order = $this->getOrder();
        if (!$order->getId()) {
            $this->norouteAction();
            return false;
        }

        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getIdealBasicQuoteId());
        $session->unsIdealBasicQuoteId();

        $order->addStatusToHistory(
            $order->getStatus(),
            Mage::helper('ideal')->__('Customer successfully returned from iDEAL')
        );

        $order->sendNewOrderEmail();

        $this->_saveInvoice($order);

        $order->save();

        $this->_redirect('checkout/onepage/success');
    }

    /**
     *  Cancel response from iDEAL
     *
     *  @return	  void
     */
    public function cancelAction()
    {
        $order = $this->getOrder();
        if (!$order->getId()) {
            $this->norouteAction();
            return false;
        }

        $order->cancel();

        $history = Mage::helper('ideal')->__('Payment was canceled by customer');

        $order->addStatusToHistory(
            $order->getStatus(),
            $history
        );

        $order->save();

        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getIdealBasicQuoteId());

        $this->_redirect('checkout/cart');
    }


    /**
     *  Error response from iDEAL
     *
     *  @return	  void
     */
    public function failureAction ()
    {
        $order = $this->getOrder();

        if (!$order->getId()) {
            $this->norouteAction();
            return false;
        }

        $order->cancel();

        $history = Mage::helper('ideal')->__('Error occured with transaction %s.', $order->getIncrementId()) . ' '
                 . Mage::helper('ideal')->__('Customer was returned from iDEAL.');

        $order->addStatusToHistory(
            $order->getStatus(),
            $history
        );

        $order->save();

        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getIdealBasicQuoteId());
        $session->setIdealErrorMessage(Mage::helper('ideal')->__('An error occurred while processing your iDEAL transaction. Please contact the web shop or try
again later. Transaction number is %s.', $order->getIncrementId()));

        $this->loadLayout();
        $this->renderLayout();

    }

    /**
     *  Save invoice for order
     *
     *  @param    Mage_Sales_Model_Order $order
     *  @return	  boolean Can save invoice or not
     */
    protected function _saveInvoice(Mage_Sales_Model_Order $order)
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
     * Notification url ... but not implemented, problems with iDEAL support
     *
     */
    public function notifyAction()
    {
        $this->norouteAction();
    }
}
