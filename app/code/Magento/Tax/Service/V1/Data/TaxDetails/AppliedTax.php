<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Service\V1\Data\TaxDetails;

class AppliedTax extends \Magento\Framework\Service\Data\AbstractExtensibleObject
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const KEY_TAX_RATE_KEY = 'tax_rate_key';

    const KEY_PERCENT = 'percent';

    const KEY_AMOUNT = 'amount';

    const KEY_RATES = 'rates';
    /**#@-*/

    /**
     * Get tax rate key
     *
     * @return string|null
     */
    public function getTaxRateKey()
    {
        return $this->_get(self::KEY_TAX_RATE_KEY);
    }

    /**
     * Get percent
     *
     * @return float
     */
    public function getPercent()
    {
        return $this->_get(self::KEY_PERCENT);
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->_get(self::KEY_AMOUNT);
    }

    /**
     * Get rates
     *
     * @return \Magento\Tax\Service\V1\Data\TaxDetails\AppliedTaxRate[]|null
     */
    public function getRates()
    {
        return $this->_get(self::KEY_RATES);
    }
}
