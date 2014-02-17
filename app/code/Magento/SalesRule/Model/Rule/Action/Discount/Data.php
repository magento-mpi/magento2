<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesRule\Model\Discount;

class Data
{
    /**
     * @var float
     */
    protected $amount;

    /**
     * @var float
     */
    protected $baseAmount;

    /**
     * @var float
     */
    protected $originalAmount;

    /**
     * @var float
     */
    protected $baseOriginalAmount;

    /**
     * @param float $amount
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $baseAmount
     * @return $this
     */
    public function setBaseAmount($baseAmount)
    {
        $this->baseAmount = $baseAmount;
        return $this;
    }

    /**
     * @return float
     */
    public function getBaseAmount()
    {
        return $this->baseAmount;
    }

    /**
     * @param float $originalAmount
     * @return $this
     */
    public function setOriginalAmount($originalAmount)
    {
        $this->originalAmount = $originalAmount;
        return $this;
    }

    /**
     * Get discount for original price
     *
     * @return float
     */
    public function getOriginalAmount()
    {
        return $this->originalAmount;
    }

    /**
     * @param float $baseOriginalAmount
     * @return $this
     */
    public function setBaseOriginalAmount($baseOriginalAmount)
    {
        $this->baseOriginalAmount = $baseOriginalAmount;
        return $this;
    }

    /**
     * Get discount for original price
     *
     * @return float
     */
    public function getBaseOriginalAmount()
    {
        return $this->baseOriginalAmount;
    }
}
