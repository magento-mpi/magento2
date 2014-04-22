<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing\Price;

use Magento\Pricing\Object\SaleableInterface;
use Magento\Pricing\Amount\AmountInterface;
use Magento\Pricing\Adjustment\CalculatorInterface;
use Magento\Pricing\PriceInfoInterface;

/**
 * Class AbstractPrice
 * Should be the base for creating any Price type class
 */
abstract class AbstractPrice implements PriceInterface
{
    /**
     * Default price type
     */
    const PRICE_CODE = 'abstract_price';

    /**
     * @var AmountInterface
     */
    protected $amount;

    /**
     * @var \Magento\Pricing\Adjustment\Calculator
     */
    protected $calculator;

    /**
     * @var SaleableInterface
     */
    protected $product;

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
     * @param SaleableInterface $saleableItem
     * @param float $quantity
     * @param CalculatorInterface $calculator
     */
    public function __construct(
        SaleableInterface $saleableItem,
        $quantity,
        CalculatorInterface $calculator
    ) {
        $this->product = $saleableItem;
        $this->quantity = $quantity;
        $this->calculator = $calculator;
        $this->priceInfo = $saleableItem->getPriceInfo();
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
            $this->amount = $this->calculator->getAmount($this->getValue(), $this->product);
        }
        return $this->amount;
    }

    /**
     * @param float $amount
     * @param null|bool|string $exclude
     * @return AmountInterface|bool|float
     */
    public function getCustomAmount($amount = null, $exclude = null)
    {
        $amount = (null === $amount) ? $this->getValue() : $amount;
        return $this->calculator->getAmount($amount, $this->product, $exclude);
    }

    /**
     * Get price type code
     *
     * @return string
     */
    public function getPriceCode()
    {
        return static::PRICE_CODE;
    }
}
