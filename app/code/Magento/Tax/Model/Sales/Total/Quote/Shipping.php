<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\Sales\Total\Quote;

use Magento\Sales\Model\Quote\Address;
use Magento\Tax\Model\Calculation;

class Shipping extends CommonTaxCollector
{
    /**
     * Collect tax totals for shipping. The result can be used to calculate discount on shipping
     *
     * @param   Address $address
     * @return  $this
     */
    public function collect(Address $address)
    {
        parent::collect($address);
        $items = $this->_getAddressItems($address);
        if (!$items) {
            return $this;
        }

        //Add shipping
        $shippingDataObject = $this->getShippingDataObject($address, false);
        $baseShippingDataObject = $this->getShippingDataObject($address, false);
        if ($shippingDataObject == null || $baseShippingDataObject == null) {
            return $this;
        }

        $quoteDetails = $this->prepareQuoteDetails($address, [$shippingDataObject]);
        $taxDetails = $this->taxCalculationService
            ->calculateTax($quoteDetails, $address->getQuote()->getStore()->getStoreId());

        $baseQuoteDetails = $this->prepareQuoteDetails($address, [$baseShippingDataObject]);
        $baseTaxDetails = $this->taxCalculationService
            ->calculateTax($baseQuoteDetails, $address->getQuote()->getStore()->getStoreId());

        $this->processShippingTaxInfo(
            $address,
            $taxDetails->getItems()[self::ITEM_CODE_SHIPPING],
            $baseTaxDetails->getItems()[self::ITEM_CODE_SHIPPING]
        );

        return $this;
    }
}
