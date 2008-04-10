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
 * @name       Mage_Protx_Form_Controller
 * @date       Fri Apr 04 15:46:14 EEST 2008
 */
class Mage_Protx_StandardController extends Mage_Core_Controller_Front_Action
{
    /**
     * Get singleton with protx strandard
     *
     * @return Mage_Protx_Model_Standard
     */
    public function getStandard()
    {
        return Mage::getSingleton('protx/standard');
    }

    public function getConfig()
    {
        return $this->getStandard()->getConfig();
    }

    /**
     *
     *  @param    none
     *  @return	  boolean
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
     *   [VendorTxCode] => magento77771148
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
     *  @param    none
     *  @return	  void
     *  @date	  Tue Apr 08 15:06:36 EEST 2008
     */
    public function  successResponseAction()
    {
        $this->preResponse();

        $responseQuery = $this->getRequest()->getQuery();
        $responseArr = $this->getStandard()->cryptToArray($responseQuery['crypt']);

        $transactionId = $responseArr['VendorTxCode'];

        if ($this->getDebug()) {
            Mage::getModel('protx/api_debug')
                ->setResponseBody(print_r($responseArr,1))
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

        if (sprintf('%.2f', $responseArr['Amount']) != sprintf('%.2f', $order->getGrandTotal())) {
            $order->addStatusToHistory(
                $order->getStatus(),
                Mage::helper('protx')->__('Order total amount does not match protx gross total amount')
            );
        } else {
            $order->getPayment()->setTransactionId($responseArr['VPSTxId']);
            if ($this->getConfig()->getPaymentType() == Mage_Protx_Model_Config::PAYMENT_TYPE_PAYMENT) {
                $this->saveInvoice($order);
            } else {
                $order->addStatusToHistory(
                    $this->getConfig()->getNewOrderStatus(), //update order status to processing after creating an invoice
                    Mage::helper('protx')->__('Order '.$invoice->getIncrementId().' has pending status')
                );
            }
        }

        $order->save();

        Mage::getSingleton('protx/session')
            ->addSuccess($this->__('You order was paid successfully'));
        $this->_redirect('protx/standard/success');
    }

    /**
     *  Save invoice for order
     *
     *  @param    Mage_Sales_Model_Order $order
     *  @return	  boolean
     *  @date	  Tue Apr 08 20:26:14 EEST 2008
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
     *  @param    none
     *  @return	  void
     *  @date	  Tue Apr 08 21:43:22 EEST 2008
     */
    public function failureResponseAction ()
    {
        $this->preResponse();

        $responseQuery = $this->getRequest()->getQuery();
        $responseArr = $this->getStandard()->cryptToArray($responseQuery['crypt']);

        $transactionId = $responseArr['VendorTxCode'];

        if ($this->getDebug()) {
            Mage::getModel('protx/api_debug')
                ->setResponseBody(print_r($responseArr,1))
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

        if ($responseArr['Status'] == 'ABORT') {
            /*$order->addStatusToHistory(
                Mage_Sales_Model_Order::STATE_CANCELED,
                Mage::helper('protx')->__('Order '.$order->getId().' was canceled by customer')
            );*/
        }

        $order->cancel()->save();

        Mage::getSingleton('protx/session')
            ->addError($this->__($responseArr['StatusDetail']));
        $this->_redirect('protx/standard/failure');
    }

    /**
     *  Pre actio
     *
     *  @param    none
     *  @return	  void
     *  @date	  Tue Apr 08 20:54:40 EEST 2008
     */
    protected function preResponse ()
    {
        if (!$this->getRequest()->isGet()) {
            $this->_redirect('');
            return;
        }
    }

    public function successAction ()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('protx/session');
        $this->renderLayout();
    }

    public function failureAction ()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('protx/session');
        $this->renderLayout();
    }
}