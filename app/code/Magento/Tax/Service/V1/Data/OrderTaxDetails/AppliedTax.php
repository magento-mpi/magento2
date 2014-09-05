<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Service\V1\Data\OrderTaxDetails;

class AppliedTax extends \Magento\Framework\Service\Data\AbstractExtensibleObject
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const KEY_CODE = 'code';

    const KEY_TITLE = 'title';

    const KEY_PERCENT = 'percent';

    const KEY_AMOUNT = 'amount';

    const KEY_BASE_AMOUNT = 'base_amount';
    /**#@-*/

    /**
     * Get code
     *
     * @return string|null
     */
    public function getCode()
    {
        return $this->_get(self::KEY_CODE);
    }

    /**
     * Get Title
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->_get(self::KEY_TITLE);
    }

    /**
     * Get Tax Percent
     *
     * @return float|null
     */
    public function getPercent()
    {
        return $this->_get(self::KEY_PERCENT);
    }

    /**
     * Get tax amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->_get(self::KEY_AMOUNT);
    }

    /**
     * Get tax amount in base currency
     *
     * @return float
     */
    public function getBaseAmount()
    {
        return $this->_get(self::KEY_BASE_AMOUNT);
    }
}
