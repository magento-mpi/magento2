<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Paypal Express Onepage checkout block
 *
 * @category   Magento
 * @package    Magento_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Paypal\Block\Express;

class Review extends \Magento\View\Element\Template
{
    /**
     * @var \Magento\Sales\Model\Quote
     */
    protected $_quote;

    /**
     * Currently selected shipping rate
     *
     * @var \Magento\Sales\Model\Quote\Address\Rate
     */
    protected $_currentShippingRate = null;

    /**
     * Paypal action prefix
     *
     * @var string
     */
    protected $_paypalActionPrefix = 'paypal';

    /**
     * @var \Magento\Tax\Helper\Data
     */
    protected $_taxHelper;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Tax\Helper\Data $taxHelper
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Tax\Helper\Data $taxHelper,
        array $data = array()
    ) {
        $this->_taxHelper = $taxHelper;
        parent::__construct($context, $data);
    }

    /**
     * Quote object setter
     *
     * @param \Magento\Sales\Model\Quote $quote
     * @return \Magento\Paypal\Block\Express\Review
     */
    public function setQuote(\Magento\Sales\Model\Quote $quote)
    {
        $this->_quote = $quote;
        return $this;
    }

    /**
     * Return quote billing address
     *
     * @return \Magento\Sales\Model\Quote\Address
     */
    public function getBillingAddress()
    {
        return $this->_quote->getBillingAddress();
    }

    /**
     * Return quote shipping address
     *
     * @return \Magento\Sales\Model\Quote\Address
     */
    public function getShippingAddress()
    {
        if ($this->_quote->getIsVirtual()) {
            return false;
        }
        return $this->_quote->getShippingAddress();
    }

    /**
     * Get HTML output for specified address
     *
     * @param \Magento\Sales\Model\Quote\Address $address
     * @return string
     */
    public function renderAddress($address)
    {
        return $address->format('html');
    }

    /**
     * Return carrier name from config, base on carrier code
     *
     * @param string $carrierCode
     * @return string
     */
    public function getCarrierName($carrierCode)
    {
        if ($name = $this->_storeConfig->getConfig("carriers/{$carrierCode}/title")) {
            return $name;
        }
        return $carrierCode;
    }

    /**
     * Get either shipping rate code or empty value on error
     *
     * @param \Magento\Object $rate
     * @return string
     */
    public function renderShippingRateValue(\Magento\Object $rate)
    {
        if ($rate->getErrorMessage()) {
            return '';
        }
        return $rate->getCode();
    }

    /**
     * Get shipping rate code title and its price or error message
     *
     * @param \Magento\Object $rate
     * @param string $format
     * @param string $inclTaxFormat
     * @return string
     */
    public function renderShippingRateOption($rate, $format = '%s - %s%s', $inclTaxFormat = ' (%s %s)')
    {
        $renderedInclTax = '';
        if ($rate->getErrorMessage()) {
            $price = $rate->getErrorMessage();
        } else {
            $price = $this->_getShippingPrice($rate->getPrice(),
                $this->_taxHelper->displayShippingPriceIncludingTax());

            $incl = $this->_getShippingPrice($rate->getPrice(), true);
            if (($incl != $price) && $this->_taxHelper->displayShippingBothPrices()) {
                $renderedInclTax = sprintf(
                    $inclTaxFormat,
                    __('Incl. Tax'),
                    $incl
                );
            }
        }
        return sprintf($format, $this->escapeHtml($rate->getMethodTitle()), $price, $renderedInclTax);
    }

    /**
     * Getter for current shipping rate
     *
     * @return \Magento\Sales\Model\Quote\Address\Rate
     */
    public function getCurrentShippingRate()
    {
        return $this->_currentShippingRate;
    }

    /**
     * Set paypal actions prefix
     */
    public function setPaypalActionPrefix($prefix)
    {
        $this->_paypalActionPrefix = $prefix;
    }

    /**
     * Return formatted shipping price
     *
     * @param float $price
     * @param bool $isInclTax
     *
     * @return bool
     */
    protected function _getShippingPrice($price, $isInclTax)
    {
        return $this->_formatPrice(
            $this->_taxHelper->getShippingPrice(
                $price,
                $isInclTax,
                $this->_address
            )
        );
    }

    /**
     * Format price base on store convert price method
     *
     * @param float $price
     * @return string
     */
    protected function _formatPrice($price)
    {
        return $this->_quote->getStore()->convertPrice($price, true);
    }

    /**
     * Retrieve payment method and assign additional template values
     *
     * @return \Magento\Paypal\Block\Express\Review
     */
    protected function _beforeToHtml()
    {
        $methodInstance = $this->_quote->getPayment()->getMethodInstance();
        $this->setPaymentMethodTitle($methodInstance->getTitle());
        $this->setUpdateOrderSubmitUrl($this->getUrl("{$this->_paypalActionPrefix}/express/updateOrder"));
        $this->setUpdateShippingMethodsUrl($this->getUrl("{$this->_paypalActionPrefix}/express/updateShippingMethods"));

        $this->setShippingRateRequired(true);
        if ($this->_quote->getIsVirtual()) {
            $this->setShippingRateRequired(false);
        } else {
            // prepare shipping rates
            $this->_address = $this->_quote->getShippingAddress();
            $groups = $this->_address->getGroupedAllShippingRates();
            if ($groups && $this->_address) {
                $this->setShippingRateGroups($groups);
                // determine current selected code & name
                foreach ($groups as $code => $rates) {
                    foreach ($rates as $rate) {
                        if ($this->_address->getShippingMethod() == $rate->getCode()) {
                            $this->_currentShippingRate = $rate;
                            break(2);
                        }
                    }
                }
            }

            // misc shipping parameters
            $this->setShippingMethodSubmitUrl($this->getUrl("{$this->_paypalActionPrefix}/express/saveShippingMethod"))
                ->setCanEditShippingAddress($this->_quote->getMayEditShippingAddress())
                ->setCanEditShippingMethod($this->_quote->getMayEditShippingMethod())
            ;
        }

        $this->setEditUrl($this->getUrl("{$this->_paypalActionPrefix}/express/edit"))
            ->setPlaceOrderUrl($this->getUrl("{$this->_paypalActionPrefix}/express/placeOrder"));

        return parent::_beforeToHtml();
    }
}
