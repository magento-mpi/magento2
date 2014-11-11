<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Service\V1\Data\OrderTaxDetails;

/**
 * Builder for the AppliedTax Data Object
 *
 * @method AppliedTax create()
 */
class AppliedTaxBuilder extends \Magento\Framework\Api\ExtensibleObjectBuilder
{
    /**
     * Set tax rate code
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        return $this->_set(AppliedTax::KEY_CODE, $code);
    }

    /**
     * Set title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        return $this->_set(AppliedTax::KEY_TITLE, $title);
    }

    /**
     * Set percent
     *
     * @param float $percent
     * @return $this
     */
    public function setPercent($percent)
    {
        return $this->_set(AppliedTax::KEY_PERCENT, $percent);
    }

    /**
     * Set amount
     *
     * @param float $amount
     * @return $this
     */
    public function setAmount($amount)
    {
        return $this->_set(AppliedTax::KEY_AMOUNT, $amount);
    }

    /**
     * Set base amount
     *
     * @param float $baseAmount
     * @return $this
     */
    public function setBaseAmount($baseAmount)
    {
        return $this->_set(AppliedTax::KEY_BASE_AMOUNT, $baseAmount);
    }
}
