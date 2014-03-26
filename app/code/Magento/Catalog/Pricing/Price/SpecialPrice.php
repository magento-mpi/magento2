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

use Magento\Pricing\Object\SaleableInterface;
use Magento\Stdlib\DateTime\TimezoneInterface;

/**
 * Special price model
 */
class SpecialPrice extends Price implements SpecialPriceInterface, OriginPriceInterface
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
     * @var float|bool|null
     */
    protected $value;

    /**
     * @param SaleableInterface $salableItem
     * @param float $quantity
     * @param TimezoneInterface $localeDate
     */
    public function __construct(SaleableInterface $salableItem, $quantity, TimezoneInterface $localeDate)
    {
        $this->localeDate = $localeDate;
        parent::__construct($salableItem, $quantity);
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
                $this->value = $specialPrice;
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
