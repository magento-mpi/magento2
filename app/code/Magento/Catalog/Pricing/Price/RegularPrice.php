<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Pricing\Price;

use Magento\Pricing\Adjustment\CalculatorInterface;
use Magento\Pricing\Amount\AmountInterface;
use Magento\Pricing\Price\PriceInterface;
use Magento\Pricing\PriceInfoInterface;
use Magento\Pricing\Object\SaleableInterface;

/**
 * Class RegularPrice
 */
class RegularPrice implements PriceInterface
{
    /**
     * Default price type
     */
    const PRICE_TYPE_PRICE_DEFAULT = 'regular_price';

    /**
     * @var string
     */
    protected $priceType = self::PRICE_TYPE_PRICE_DEFAULT;

    /**
     * @var SaleableInterface|\Magento\Catalog\Model\Product
     */
    protected $salableItem;

    /**
     * @var PriceInfoInterface
     */
    protected $priceInfo;

    /**
     * @var float
     */
    protected $quantity;

    /**
     * @var \Magento\Pricing\Adjustment\Calculator
     */
    protected $calculator;

    /**
     * @var bool|float
     */
    protected $value;

    /**
     * @var AmountInterface
     */
    protected $amount;

    /**
     * @param SaleableInterface $salableItem
     * @param float $quantity
     * @param CalculatorInterface $calculator
     */
    public function __construct(
        SaleableInterface $salableItem,
        $quantity,
        CalculatorInterface $calculator
    ) {
        $this->salableItem = $salableItem;
        $this->quantity = $quantity;
        $this->calculator = $calculator;
        $this->priceInfo = $salableItem->getPriceInfo();
    }

    /**
     * Get price value
     *
     * @return float|bool
     */
    public function getValue()
    {
        if ($this->value === null) {
            $price = $this->salableItem->getPrice();
            $this->value = $price ? floatval($price) : false;
        }
        return $this->value;
    }

    /**
     * Get Price Amount object
     *
     * @return AmountInterface
     */
    public function getAmount()
    {
        if (null === $this->amount) {
            $this->amount = $this->calculator->getAmount($this->getValue(), $this->salableItem);
        }
        return $this->amount;
    }

    /**
     * @param float $amount
     * @param null|string $exclude
     * @return AmountInterface
     */
    public function getCustomAmount($amount = null, $exclude = null)
    {
        if ($amount === null) {
            $amount = $this->getValue();
        }
        return $this->calculator->getAmount($amount, $this->salableItem, $exclude);
    }

    /**
     * Get price type code
     *
     * @return string
     */
    public function getPriceType()
    {
        return $this->priceType;
    }
}
