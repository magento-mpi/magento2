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

use Magento\Pricing\Adjustment\CalculatorInterface;
use Magento\Catalog\Model\Product;
use Magento\Stdlib\DateTime\TimezoneInterface;

/**
 * Special price model
 */
class SpecialPrice extends AbstractPrice implements SpecialPriceInterface
{
    /**
     * Price type special
     */
    const PRICE_CODE = 'special_price';

    /**
     * @var TimezoneInterface
     */
    protected $localeDate;

    /**
     * @param Product $saleableItem
     * @param float $quantity
     * @param CalculatorInterface $calculator
     * @param TimezoneInterface $localeDate
     */
    public function __construct(
        Product $saleableItem,
        $quantity,
        CalculatorInterface $calculator,
        TimezoneInterface $localeDate
    ) {
        parent::__construct($saleableItem, $quantity, $calculator);
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
        return $this->product->getSpecialPrice();
    }

    /**
     * Returns starting date of the special price
     *
     * @return mixed
     */
    public function getSpecialFromDate()
    {
        return $this->product->getSpecialFromDate();
    }

    /**
     * Returns end date of the special price
     *
     * @return mixed
     */
    public function getSpecialToDate()
    {
        return $this->product->getSpecialToDate();
    }

    /**
     * @return bool
     */
    public function isScopeDateInInterval()
    {
        return $this->localeDate->isScopeDateInInterval(
            $this->product->getStore(),
            $this->getSpecialFromDate(),
            $this->getSpecialToDate()
        );
    }
}
