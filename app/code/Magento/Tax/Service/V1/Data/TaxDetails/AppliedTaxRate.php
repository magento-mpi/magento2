<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1\Data\TaxDetails;

class AppliedTaxRate extends \Magento\Framework\Api\AbstractExtensibleObject
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const KEY_CODE = 'code';

    const KEY_TITLE = 'title';

    const KEY_PERCENT = 'percent';
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
}
