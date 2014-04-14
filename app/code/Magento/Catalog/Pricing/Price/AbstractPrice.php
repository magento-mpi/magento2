<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Pricing\Price;

use Magento\Pricing\Price\PriceInterface;
use Magento\Pricing\Amount\AmountInterface;
use Magento\Pricing\Object\SaleableInterface;
use Magento\Pricing\Adjustment\CalculatorInterface;
use Magento\Pricing\PriceInfoInterface;

/**
 * Class AbstractPrice
 * Should be the base for creating any Price type class
 *
 * @package Magento\Catalog\Pricing\Price
 */
abstract class AbstractPrice implements PriceInterface
{
    /**
     * @var AmountInterface
     */
    protected $amount;

    /**
     * @var \Magento\Pricing\Adjustment\Calculator
     */
    protected $calculator;

    /**
     * @var SaleableInterface|\Magento\Catalog\Model\Product
     */
    protected $salableItem;

    /**
     * @var string
     */
    protected $priceType;

    /**
     * @var float
     */
    protected $quantity;

    /**
     * @var PriceInfoInterface
     */
    protected $priceInfo;

    /**
     * @var bool|float
     */
    protected $value;

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
    abstract public function getValue();

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
     * @param null|bool|string $exclude
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
        return static::PRICE_TYPE_CODE;
    }
} 