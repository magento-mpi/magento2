<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Pricing\Price;

use Magento\Framework\Pricing\Adjustment\CalculatorInterface;
use Magento\Framework\Pricing\Object\SaleableInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Special price model
 */
class SpecialPrice extends RegularPrice implements SpecialPriceInterface
{
    /**
     * @var string
     */
    protected $priceType = self::PRICE_TYPE_SPECIAL;

    /**
     * @var TimezoneInterface
     */
    protected $localeDate;

    /**
     * @param SaleableInterface $salableItem
     * @param float $quantity
     * @param CalculatorInterface $calculator
     * @param TimezoneInterface $localeDate
     */
    public function __construct(
        SaleableInterface $salableItem,
        $quantity,
        CalculatorInterface $calculator,
        TimezoneInterface $localeDate
    ) {
        parent::__construct($salableItem, $quantity, $calculator);
        $this->localeDate = $localeDate;
    }

    /**
     * @return bool|float
     */
    public function getValue()
    {
        if (null === $this->value) {
            $this->value = false;
            $specialPrice = $this->getSpecialPrice();
            if (!is_null($specialPrice) && $specialPrice !== false && $this->isScopeDateInInterval()) {
                $this->value = (float) $specialPrice;
            }
        }

        return $this->value;
    }

    /**
     * Returns special price
     *
     * @return float
     */
    public function getSpecialPrice()
    {
        return $this->salableItem->getSpecialPrice();
    }

    /**
     * Returns starting date of the special price
     *
     * @return mixed
     */
    public function getSpecialFromDate()
    {
        return $this->salableItem->getSpecialFromDate();
    }

    /**
     * Returns end date of the special price
     *
     * @return mixed
     */
    public function getSpecialToDate()
    {
        return $this->salableItem->getSpecialToDate();
    }

    /**
     * @return bool
     */
    public function isScopeDateInInterval()
    {
        return $this->localeDate->isScopeDateInInterval(
            $this->salableItem->getStore(),
            $this->getSpecialFromDate(),
            $this->getSpecialToDate()
        );
    }
}
