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
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Sales_Model_Order_Payment extends Mage_Core_Model_Abstract
{
    protected $_order;

    protected function _construct()
    {
        $this->_init('sales/order_payment');
    }

    public function setOrder(Mage_Sales_Model_Order $order)
    {
        $this->_order = $order;
        return $this;
    }

    public function getOrder()
    {
        return $this->_order;
    }

    public function importQuotePayment(Mage_Sales_Model_Quote_Payment $newPayment)
    {
        $payment = clone $newPayment;
        $payment->unsEntityId()
            ->unsAttributeSetId()
            ->unsEntityTypeId()
            ->unsParentId();

        $this->addData($payment->getData());
        return $this;
    }

    public function getCcNumber()
    {
        if (!$this->getData('cc_number') && $this->getData('cc_number_enc')) {
            $customerPayment = Mage::getModel('customer/payment');
            $this->setData('cc_number', $customerPayment->decrypt($this->getData('cc_number_enc')));
        }
        return $this->getData('cc_number');
    }

    public function getCcCid()
    {
        if (!$this->getData('cc_cid') && $this->getData('cc_cid_enc')) {
            $customerPayment = Mage::getModel('customer/payment');
            $this->setData('cc_cid', $customerPayment->decrypt($this->getData('cc_cid_enc')));
        }
        return $this->getData('cc_cid');
    }

    public function getHtmlFormated()
    {
        $html = '';
        /**
         * @todo remove this !!!!!! only temporary
         */
        $methodConfig = new Varien_Object(
            Mage::getStoreConfig('payment/' . $this->getMethod(), $this->getOrder()->getStoreId())
        );
        if ($methodConfig) {
            $className = $methodConfig->getModel();
            $method = Mage::getModel($className);
            if ($method) {
                $html = '<p>'.Mage::getStoreConfig('payment/' . $this->getMethod() . '/title').'</p>';
                $method->setPayment($this);
                $methodBlock = $method->createInfoBlock('payment.method.'.$this->getMethod().'.'.$this->getId());
                if (!empty($methodBlock)) {
                    $html .= $methodBlock->toHtml();
                }
            }
        }
        return $html;
        //return Mage::getHelper('payment/info_cc')->setPayment($this)->toHtml();
    }
}