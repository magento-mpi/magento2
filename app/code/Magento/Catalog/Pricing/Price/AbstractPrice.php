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

use Magento\Pricing\Adjustment\AdjustmentInterface;
use Magento\Catalog\Model\Product;
use Magento\Pricing\Price\PriceInterface;
use Magento\Pricing\PriceInfoInterface;

/**
 * Abstract catalog price model
 */
class AbstractPrice implements PriceInterface
{
    /**
     * @var string
     */
    protected $priceType;

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var PriceInfoInterface
     */
    protected $priceInfo;

    /**
     * @var AdjustmentInterface[]
     */
    protected $adjustments;

    /**
     * @var float
     */
    protected $baseAmount = 0.00;

    /**
     * @var float
     */
    protected $adjustedAmount = 0.00;

    /**
     * @var float[]
     */
    protected $adjustedAmounts = [];

    /**
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
        $this->priceInfo = $product->getPriceInfo();
        $this->baseAmount = $this->getValue();

        $adjustments = [];
        foreach (array_reverse($this->priceInfo->getAdjustments()) as $adjustment) {
            /** @var AdjustmentInterface $adjustment */
            if ($adjustment->isIncludedInBasePrice()) {
                $code = $adjustment->getAdjustmentCode();
                $adjustments[$code] = $adjustment;
                $adjustedAmount = $adjustment->extractAdjustment($this->baseAmount, $this->product);
                $this->baseAmount = $this->baseAmount - $adjustedAmount;
                $this->adjustedAmount = + $adjustedAmount;
                $this->adjustedAmounts[$code] = $adjustedAmount;
            }
        }
        $this->adjustments = $adjustments;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriceType()
    {
        return $this->priceType;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->product->getDataUsingMethod($this->priceType);
    }

    /**
     * {@inheritdoc}
     */
    public function getDisplayValue($excludedCode = null)
    {
        $amount = $this->baseAmount;
        foreach ($this->priceInfo->getAdjustments() as $adjustment) {
            $code = $adjustment->getAdjustmentCode();
            $exclude = false;
            if ($excludedCode && $adjustment->isExcludedWith($excludedCode)) {
                $exclude = true;
            }
            if ($adjustment->isIncludedInDisplayPrice($this->product) && !$exclude) {
                if (isset($this->adjustedAmounts[$code])) {
                    $amount = $amount + $this->adjustedAmounts[$code];
                } else {
                    $amount = $adjustment->applyAdjustment($amount, $this->product);
                }
            }
        }
        return $amount;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string) $this->getDisplayValue();
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseAmount()
    {
        return $this->baseAmount;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalAdjustmentAmount()
    {
        return $this->adjustedAmount;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdjustments()
    {
        return $this->adjustments;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdjustment($adjustmentCode)
    {
        return isset($this->adjustments[$adjustmentCode]) ? $this->adjustments[$adjustmentCode] : false;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAdjustment($adjustmentCode)
    {
        return array_key_exists($adjustmentCode, $this->adjustments);
    }
}
