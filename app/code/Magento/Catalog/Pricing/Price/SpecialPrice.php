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
class SpecialPrice extends Price implements SpecialPriceInterface, \Magento\Catalog\Pricing\Price\OriginPrice
{
    /**
     * @var string
     */
    protected $priceType = 'special_price';

    /**
     * @var TimezoneInterface
     */
    protected $localeDate;

    /**
     * @param SaleableInterface $salableItem
     * @param TimezoneInterface $localeDate
     * @param float $quantity
     */
    public function __construct(SaleableInterface $salableItem, TimezoneInterface $localeDate, $quantity)
    {
        $this->localeDate = $localeDate;
        parent::__construct($salableItem, $quantity);
    }

    /**
     * @return bool|float|mixed
     */
    public function getValue()
    {
        $specialPrice = $this->getSpecialPrice();
        if (!is_null($specialPrice) && $specialPrice != false) {
            if ($this->localeDate->isScopeDateInInterval(
                $this->salableItem->getStore(),
                $this->getSpecialFromDate(),
                $this->getSpecialToDate()
            )) {
                return $specialPrice;
            }
        }
        return false;
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
}
