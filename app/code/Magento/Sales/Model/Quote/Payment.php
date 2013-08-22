<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Quote payment information
 *
 * @method Magento_Sales_Model_Resource_Quote_Payment _getResource()
 * @method Magento_Sales_Model_Resource_Quote_Payment getResource()
 * @method int getQuoteId()
 * @method Magento_Sales_Model_Quote_Payment setQuoteId(int $value)
 * @method string getCreatedAt()
 * @method Magento_Sales_Model_Quote_Payment setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method Magento_Sales_Model_Quote_Payment setUpdatedAt(string $value)
 * @method string getMethod()
 * @method Magento_Sales_Model_Quote_Payment setMethod(string $value)
 * @method string getCcType()
 * @method Magento_Sales_Model_Quote_Payment setCcType(string $value)
 * @method string getCcNumberEnc()
 * @method Magento_Sales_Model_Quote_Payment setCcNumberEnc(string $value)
 * @method string getCcLast4()
 * @method Magento_Sales_Model_Quote_Payment setCcLast4(string $value)
 * @method string getCcCidEnc()
 * @method Magento_Sales_Model_Quote_Payment setCcCidEnc(string $value)
 * @method string getCcSsOwner()
 * @method Magento_Sales_Model_Quote_Payment setCcSsOwner(string $value)
 * @method int getCcSsStartMonth()
 * @method Magento_Sales_Model_Quote_Payment setCcSsStartMonth(int $value)
 * @method int getCcSsStartYear()
 * @method Magento_Sales_Model_Quote_Payment setCcSsStartYear(int $value)
 * @method string getPaypalCorrelationId()
 * @method Magento_Sales_Model_Quote_Payment setPaypalCorrelationId(string $value)
 * @method string getPaypalPayerId()
 * @method Magento_Sales_Model_Quote_Payment setPaypalPayerId(string $value)
 * @method string getPaypalPayerStatus()
 * @method Magento_Sales_Model_Quote_Payment setPaypalPayerStatus(string $value)
 * @method string getPoNumber()
 * @method Magento_Sales_Model_Quote_Payment setPoNumber(string $value)
 * @method string getAdditionalData()
 * @method Magento_Sales_Model_Quote_Payment setAdditionalData(string $value)
 * @method string getCcSsIssue()
 * @method Magento_Sales_Model_Quote_Payment setCcSsIssue(string $value)
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Quote_Payment extends Magento_Payment_Model_Info
{
    protected $_eventPrefix = 'sales_quote_payment';
    protected $_eventObject = 'payment';

    protected $_quote;

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Magento_Sales_Model_Resource_Quote_Payment');
    }

    /**
     * Declare quote model instance
     *
     * @param   Magento_Sales_Model_Quote $quote
     * @return  Magento_Sales_Model_Quote_Payment
     */
    public function setQuote(Magento_Sales_Model_Quote $quote)
    {
        $this->_quote = $quote;
        $this->setQuoteId($quote->getId());
        return $this;
    }

    /**
     * Retrieve quote model instance
     *
     * @return Magento_Sales_Model_Quote
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
     * @throws  Magento_Core_Exception
     * @return  Magento_Sales_Model_Quote_Payment
     */
    public function importData(array $data)
    {
        $data = new Magento_Object($data);
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
         * Payment availability related with quote totals.
         * We have to recollect quote totals before checking
         */
        $this->getQuote()->collectTotals();

        if (!$method->isAvailable($this->getQuote())
            || !$method->isApplicableToQuote($this->getQuote(), $data->getChecks())
        ) {
            Mage::throwException(__('The requested Payment Method is not available.'));
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
     * @return Magento_Sales_Model_Quote_Payment
     */
    protected function _beforeSave()
    {
        if ($this->getQuote()) {
            $this->setQuoteId($this->getQuote()->getId());
        }
        try {
            $method = $this->getMethodInstance();
        } catch (Magento_Core_Exception $e) {
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
     * @return Magento_Payment_Model_Method_Abstract
     */
    public function getMethodInstance()
    {
        $method = parent::getMethodInstance();
        return $method->setStore($this->getQuote()->getStore());
    }
}
