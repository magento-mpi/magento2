<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing\PriceInfo;

use Magento\Pricing\PriceInfoInterface;
use Magento\Pricing\PriceComposite;
use Magento\Pricing\Price\PriceInterface;
use Magento\Pricing\AdjustmentComposite;
use Magento\Pricing\Adjustment\AdjustmentInterface;
use Magento\Pricing\Object\SaleableInterface;

/**
 * Price info base model
 */
class Base implements PriceInfoInterface
{
    /**
     * @var SaleableInterface
     */
    protected $product;

    /**
     * @var PriceComposite
     */
    protected $prices;

    /**
     * @var PriceInterface[]
     */
    protected $priceInstances;

    /**
     * @var AdjustmentComposite
     */
    protected $adjustments;

    /**
     * @param SaleableInterface $product
     * @param PriceComposite $prices
     * @param AdjustmentComposite $adjustments
     */
    public function __construct(
        SaleableInterface $product,
        PriceComposite $prices,
        AdjustmentComposite $adjustments
    ) {
        $this->product = $product;
        $this->prices = $prices;
        $this->adjustments = $adjustments;
    }

    /**
     * @return PriceInterface[]
     */
    public function getPrices()
    {
        // check if all prices initialized
        $this->initPrices();
        return $this->priceInstances;
    }

    /**
     * Init price types
     *
     * @return $this
     */
    protected function initPrices()
    {
        $prices = $this->prices->getPriceCodes();
        foreach ($prices as $code) {
            if (!isset($this->priceInstances[$code])) {
                $this->priceInstances[$code] = $this->prices->createPriceObject($code, $this->product);
            }
        }
        return $this;
    }

    /**
     * @param string $priceCode
     * @return PriceInterface
     */
    public function getPrice($priceCode)
    {
        if (!isset($this->priceInstances[$priceCode])) {
            $this->priceInstances[$priceCode] = $this->prices->createPriceObject($priceCode, $this->product);
        }
        return $this->priceInstances[$priceCode];
    }

    /**
     * Get all registered adjustments
     *
     * @return AdjustmentInterface[]
     */
    public function getAdjustments()
    {
        return $this->adjustments->getAdjustments();
    }

    /**
     * Get adjustment by code
     *
     * @param string $adjustmentCode
     * @throws \InvalidArgumentException
     * @return AdjustmentInterface
     */
    public function getAdjustment($adjustmentCode)
    {
        $adjustments = $this->adjustments->getAdjustments();
        if (!isset($adjustments[$adjustmentCode])) {
            throw new \InvalidArgumentException($adjustmentCode . ' is not registered adjustment');
        }
        return $adjustments[$adjustmentCode];
    }
}
