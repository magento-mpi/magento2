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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Quote payment information
 *
 * @method Mage_Sales_Model_Resource_Quote_Payment _getResource()
 * @method Mage_Sales_Model_Resource_Quote_Payment getResource()
 * @method Mage_Sales_Model_Quote_Payment getQuoteId()
 * @method int setQuoteId(int $value)
 * @method Mage_Sales_Model_Quote_Payment getCreatedAt()
 * @method string setCreatedAt(string $value)
 * @method Mage_Sales_Model_Quote_Payment getUpdatedAt()
 * @method string setUpdatedAt(string $value)
 * @method Mage_Sales_Model_Quote_Payment getMethod()
 * @method string setMethod(string $value)
 * @method Mage_Sales_Model_Quote_Payment getCcType()
 * @method string setCcType(string $value)
 * @method Mage_Sales_Model_Quote_Payment getCcNumberEnc()
 * @method string setCcNumberEnc(string $value)
 * @method Mage_Sales_Model_Quote_Payment getCcLast4()
 * @method string setCcLast4(string $value)
 * @method Mage_Sales_Model_Quote_Payment getCcCidEnc()
 * @method string setCcCidEnc(string $value)
 * @method Mage_Sales_Model_Quote_Payment getCcOwner()
 * @method string setCcOwner(string $value)
 * @method Mage_Sales_Model_Quote_Payment getCcExpMonth()
 * @method int setCcExpMonth(int $value)
 * @method Mage_Sales_Model_Quote_Payment getCcExpYear()
 * @method int setCcExpYear(int $value)
 * @method Mage_Sales_Model_Quote_Payment getCcSsOwner()
 * @method string setCcSsOwner(string $value)
 * @method Mage_Sales_Model_Quote_Payment getCcSsStartMonth()
 * @method int setCcSsStartMonth(int $value)
 * @method Mage_Sales_Model_Quote_Payment getCcSsStartYear()
 * @method int setCcSsStartYear(int $value)
 * @method Mage_Sales_Model_Quote_Payment getCybersourceToken()
 * @method string setCybersourceToken(string $value)
 * @method Mage_Sales_Model_Quote_Payment getPaypalCorrelationId()
 * @method string setPaypalCorrelationId(string $value)
 * @method Mage_Sales_Model_Quote_Payment getPaypalPayerId()
 * @method string setPaypalPayerId(string $value)
 * @method Mage_Sales_Model_Quote_Payment getPaypalPayerStatus()
 * @method string setPaypalPayerStatus(string $value)
 * @method Mage_Sales_Model_Quote_Payment getPoNumber()
 * @method string setPoNumber(string $value)
 * @method Mage_Sales_Model_Quote_Payment getAdditionalData()
 * @method string setAdditionalData(string $value)
 * @method Mage_Sales_Model_Quote_Payment getCcSsIssue()
 * @method string setCcSsIssue(string $value)
 * @method Mage_Sales_Model_Quote_Payment getIdealIssuerId()
 * @method string setIdealIssuerId(string $value)
 * @method Mage_Sales_Model_Quote_Payment getIdealIssuerList()
 * @method string setIdealIssuerList(string $value)
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Quote_Payment extends Mage_Payment_Model_Info
{
    protected $_eventPrefix = 'sales_quote_payment';
    protected $_eventObject = 'payment';

    protected $_quote;

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('sales/quote_payment');
    }

    /**
     * Declare quote model instance
     *
     * @param   Mage_Sales_Model_Quote $quote
     * @return  Mage_Sales_Model_Quote_Payment
     */
    public function setQuote(Mage_Sales_Model_Quote $quote)
    {
        $this->_quote = $quote;
        $this->setQuoteId($quote->getId());
        return $this;
    }

    /**
     * Retrieve quote model instance
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->_quote;
    }

    /**
     * Import data array to payment method object,
     * Method calls quote totals collect because payment method availability
     * can be related to quote totals
     *
     * @param   array $data
     * @throws  Mage_Core_Exception
     * @return  Mage_Sales_Model_Quote_Payment
     */
    public function importData(array $data)
    {
        $data = new Varien_Object($data);
        Mage::dispatchEvent(
            $this->_eventPrefix . '_import_data_before',
            array(
                $this->_eventObject=>$this,
                'input'=>$data,
            )
        );

        $this->setMethod($data->getMethod());
        $method = $this->getMethodInstance();

        /**
         * Payment avalability related with quote totals.
         * We have recollect quote totals before checking
         */
        $this->getQuote()->collectTotals();

        if (!$method->isAvailable($this->getQuote())) {
            Mage::throwException(Mage::helper('sales')->__('The requested Payment Method is not available.'));
        }

        $method->assignData($data);
        /*
        * validating the payment data
        */
        $method->validate();
        return $this;
    }

    /**
     * Prepare object for save
     *
     * @return Mage_Sales_Model_Quote_Payment
     */
    protected function _beforeSave()
    {
        if ($this->getQuote()) {
            $this->setQuoteId($this->getQuote()->getId());
        }
        try {
            $method = $this->getMethodInstance();
        } catch (Mage_Core_Exception $e) {
            return parent::_beforeSave();
        }
        $method->prepareSave();
        return parent::_beforeSave();
    }

    /**
     * Checkout redirect URL getter
     *
     * @return string
     */
    public function getCheckoutRedirectUrl()
    {
        $method = $this->getMethodInstance();
        if ($method) {
            return $method->getCheckoutRedirectUrl();
        }
        return '';
    }

    /**
     * Checkout order place redirect URL getter
     *
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
        $method = $this->getMethodInstance();
        if ($method) {
            return $method->getOrderPlaceRedirectUrl();
        }
        return '';
    }

    /**
     * Retrieve payment method model object
     *
     * @return Mage_Payment_Model_Method_Abstract
     */
    public function getMethodInstance()
    {
        $method = parent::getMethodInstance();
        return $method->setStore($this->getQuote()->getStore());
    }
}
