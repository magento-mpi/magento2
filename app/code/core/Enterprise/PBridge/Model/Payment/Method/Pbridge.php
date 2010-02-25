<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_PBridge
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Pbridge payment method model
 *
 * @category    Enterprise
 * @package     Enterprise_PBridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Pbridge_Model_Payment_Method_Pbridge extends Mage_Payment_Model_Method_Abstract
{
    /**
     * Payment code name
     *
     * @var string
     */
    protected $_code = 'pbridge';

    /**
     * Payment method instance wrapped by Payment Bridge
     *
     * @var Mage_Payment_Model_Method_Abstract
     */
    protected $_originalMethodInstance = null;

    /**
     * Code for wrapped payment method
     *
     * @var string
     */
    protected $_originalMethodCode = null;

    /**
     * Payment Bridge call for wrapped payment operations
     *
     * @param array $params
     */
    protected function _call($params)
    {
    }

    /**
     * Assign data to info model instance
     *
     * @param  mixed $data
     * @return Mage_Payment_Model_Info
     */
    public function assignData($data)
    {
        $pbridgeData = array();
        if (is_array($data)) {
            if (isset($data['pbridge_data'])) {
                $pbridgeData = $data['pbridge_data'];
                unset($data['pbridge_data']);
            }
        } else {
            $pbridgeData = $data->getData('pbridge_data');
            $data->unsetData('pbridge_data');
        }
        parent::assignData($data);
        $this->setInfoAdditionalData($pbridgeData);
        return $this;
    }

    /**
     * Save additional Payment Bridge parameters into the Info instance additional data storage
     *
     * @param array $data
     * @return Enterprise_Pbridge_Model_Payment_Method_Pbridge
     */
    public function setInfoAdditionalData($data)
    {
        if (empty($data)) {
            return $this;
        }
        $additionaData = $this->getInfoInstance()->getAdditionalData();
        if ($additionaData) {
            $additionalData = array_merge(unserialize($additionaData), array('pbridge_data' => $data));
        } else {
            $additionalData = array('pbridge_data' => $data);
        }
        $this->getInfoInstance()->setAdditionalData(serialize($additionalData));
        return $this;
    }

    /**
     * Retrieve additional Payment Bridge parameters from the Info instance additional data storage
     *
     * @param string $param OPTIONAL
     * @return mixed
     */
    public function getInfoAdditionalData($param = null)
    {
        $additionaData = $this->getInfoInstance()->getAdditionalData();
        if (!$additionaData) {
            return null;
        }
        $additionaData = unserialize($additionaData);
        if (!isset($additionaData['pbridge_data'])) {
            return null;
        }
        if (null === $param) {
            return $additionaData['pbridge_data'];
        }
        return isset($additionaData['pbridge_data'][$param]) ? $additionaData['pbridge_data'][$param] : null;
    }

    /**
     * Retrieve Payment Bridge token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->getInfoAdditionalData('token');
    }

    /**
     * Getter.
     * Retrieve the wrapped payment method instance
     *
     * @return Mage_Payment_Model_Method_Abstract
     * @throws Exception
     */
    public function getOriginalMethodInstance()
    {
        if ($this->_originalMethodInstance === null) {
            $this->_originalMethodCode = $this->getInfoAdditionalData('original_payment_method');
            if ($this->_originalMethodCode === null) {
                Mage::throwException('Payment method code is not specified.');
            }
            $this->_originalMethodInstance = Mage::helper('payment')
                ->getMethodInstance($this->_originalMethodCode);
        }
        return $this->_originalMethodInstance;
    }

    /**
     * Get config peyment action url
     *
     * @return string
     */
    public function getConfigPaymentAction()
    {
        return $this->getOriginalMethodInstance()->getConfigPaymentAction();
    }

    /**
     * Check authorise availability
     *
     * @return bool
     */
    public function canAuthorize()
    {
        return $this->getOriginalMethodInstance()->canAuthorize();
    }

    /**
     * Check capture availability
     *
     * @return bool
     */
    public function canCapture()
    {
        return $this->getOriginalMethodInstance()->canCapture();
    }

    /**
     * Check partial capture availability
     *
     * @return bool
     */
    public function canCapturePartial()
    {
        return $this->getOriginalMethodInstance()->canCapturePartial();
    }

    /**
     * Check refund availability
     *
     * @return bool
     */
    public function canRefund()
    {
        return $this->getOriginalMethodInstance()->canRefund();
    }

    /**
     * Check partial refund availability for invoice
     *
     * @return bool
     */
    public function canRefundPartialPerInvoice()
    {
        return $this->getOriginalMethodInstance()->canRefundPartialPerInvoice();
    }

    /**
     * Check void availability
     *
     * @param   Varien_Object $invoicePayment
     * @return  bool
     */
    public function canVoid(Varien_Object $payment)
    {
        return $this->getOriginalMethodInstance()->canVoid($payment);
    }

    /**
     * Using internal pages for input payment data
     * Can be used in admin
     *
     * @return bool
     */
    public function canUseInternal()
    {
        return $this->getOriginalMethodInstance()->canUseInternal();
    }

    /**
     * Can be used in regular checkout
     *
     * @return bool
     */
    public function canUseCheckout()
    {
        return $this->getOriginalMethodInstance()->canUseCheckout();
    }

    /**
     * Using for multiple shipping address
     *
     * @return bool
     */
    public function canUseForMultishipping()
    {
        return $this->getOriginalMethodInstance()->canUseForMultishipping();
    }

    /**
     * Can be edit order (renew order)
     *
     * @return bool
     */
    public function canEdit()
    {
        return $this->getOriginalMethodInstance()->canEdit();
    }

    /**
     * To check billing country is allowed for the payment method
     *
     * @return bool
     */
    public function canUseForCountry($country)
    {
        return $this->getOriginalMethodInstance()->canUseForCountry($country);
    }

    /**
     * Check method for processing with base currency
     *
     * @param string $currencyCode
     * @return boolean
     */
    public function canUseForCurrency($currencyCode)
    {
        return $this->getOriginalMethodInstance()->canUseForCurrency($currencyCode);
    }

    /**
     * Retrieve payment method title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getOriginalMethodInstance()->getTitle();
    }

    /**
     * Authorize
     *
     * @param   Varien_Object $orderPayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        parent::authorize($payment, $amount);
        $this->_call(array(
            'operation' => 'authorize',
            'payment'   => $payment,
            'amount'    => $amount,
        ));
        return $this;
    }

    /**
     * Cancel payment
     *
     * @param   Varien_Object $invoicePayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function cancel(Varien_Object $payment)
    {
        parent::cancel($payment);
        $this->_call(array(
            'operation' => 'cancel',
            'payment'   => $payment,
        ));
        return $this;
    }

    /**
     * Capture payment
     *
     * @param   Varien_Object $orderPayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function capture(Varien_Object $payment, $amount)
    {
        parent::capture($payment, $amount);
        $this->_call(array(
            'operation' => 'capture',
            'payment'   => $payment,
            'amount'    => $amount,
        ));
        return $this;
    }

    /**
     * Refund money
     *
     * @param   Varien_Object $invoicePayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function refund(Varien_Object $payment, $amount)
    {
        parent::refund($payment, $amount);
        $this->_call(array(
            'operation' => 'refund',
            'payment'   => $payment,
            'amount'    => $amount,
        ));
        return $this;
    }

    /**
     * Void payment
     *
     * @param   Varien_Object $invoicePayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function void(Varien_Object $payment)
    {
        parent::void($payment);
        $this->_call(array(
            'operation' => 'void',
            'payment'   => $payment,
        ));
        return $this;
    }
}
