<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Service\V1\Data\TaxDetails;

/**
 * Builder for the AppliedTaxRate Service Data Object
 *
 * @method AppliedTaxRate create()
 */
class AppliedTaxRateBuilder extends \Magento\Framework\Api\AbstractExtensibleObjectBuilder
{
    /**
     * Set code
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        return $this->_set(AppliedTaxRate::KEY_CODE, $code);
    }

    /**
     * Set title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        return $this->_set(AppliedTaxRate::KEY_TITLE, $title);
    }

    /**
     * Set tax percent
     *
     * @param float $percent
     * @return $this
     */
    public function setPercent($percent)
    {
        return $this->_set(AppliedTaxRate::KEY_PERCENT, $percent);
    }
}
