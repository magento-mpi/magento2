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
 * @method \Magento\Sales\Model\Resource\Quote\Payment _getResource()
 * @method \Magento\Sales\Model\Resource\Quote\Payment getResource()
 * @method int getQuoteId()
 * @method \Magento\Sales\Model\Quote\Payment setQuoteId(int $value)
 * @method string getCreatedAt()
 * @method \Magento\Sales\Model\Quote\Payment setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method \Magento\Sales\Model\Quote\Payment setUpdatedAt(string $value)
 * @method string getMethod()
 * @method \Magento\Sales\Model\Quote\Payment setMethod(string $value)
 * @method string getCcType()
 * @method \Magento\Sales\Model\Quote\Payment setCcType(string $value)
 * @method string getCcNumberEnc()
 * @method \Magento\Sales\Model\Quote\Payment setCcNumberEnc(string $value)
 * @method string getCcLast4()
 * @method \Magento\Sales\Model\Quote\Payment setCcLast4(string $value)
 * @method string getCcCidEnc()
 * @method \Magento\Sales\Model\Quote\Payment setCcCidEnc(string $value)
 * @method string getCcSsOwner()
 * @method \Magento\Sales\Model\Quote\Payment setCcSsOwner(string $value)
 * @method int getCcSsStartMonth()
 * @method \Magento\Sales\Model\Quote\Payment setCcSsStartMonth(int $value)
 * @method int getCcSsStartYear()
 * @method \Magento\Sales\Model\Quote\Payment setCcSsStartYear(int $value)
 * @method string getPoNumber()
 * @method \Magento\Sales\Model\Quote\Payment setPoNumber(string $value)
 * @method string getAdditionalData()
 * @method \Magento\Sales\Model\Quote\Payment setAdditionalData(string $value)
 * @method string getCcSsIssue()
 * @method \Magento\Sales\Model\Quote\Payment setCcSsIssue(string $value)
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Quote;

class Payment extends \Magento\Payment\Model\Info
{
    protected $_eventPrefix = 'sales_quote_payment';
    protected $_eventObject = 'payment';

    protected $_quote;

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Magento\Sales\Model\Resource\Quote\Payment');
    }

    /**
     * Declare quote model instance
     *
     * @param   \Magento\Sales\Model\Quote $quote
     * @return  \Magento\Sales\Model\Quote\Payment
     */
    public function setQuote(\Magento\Sales\Model\Quote $quote)
    {
        $this->_quote = $quote;
        $this->setQuoteId($quote->getId());
        return $this;
    }

    /**
     * Retrieve quote model instance
     *
     * @return \Magento\Sales\Model\Quote
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
     * @throws  \Magento\Core\Exception
     * @return  \Magento\Sales\Model\Quote\Payment
     */
    public function importData(array $data)
    {
        $data = new \Magento\Object($data);
        $this->_eventManager->dispatch(
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
            throw new \Magento\Core\Exception(__('The requested Payment Method is not available.'));
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
     * @return \Magento\Sales\Model\Quote\Payment
     */
    protected function _beforeSave()
    {
        if ($this->getQuote()) {
            $this->setQuoteId($this->getQuote()->getId());
        }
        try {
            $method = $this->getMethodInstance();
        } catch (\Magento\Core\Exception $e) {
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
     * @return \Magento\Payment\Model\Method\AbstractMethod
     */
    public function getMethodInstance()
    {
        $method = parent::getMethodInstance();
        return $method->setStore($this->getQuote()->getStore());
    }
}
