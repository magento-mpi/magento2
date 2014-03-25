<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing;

use Magento\Pricing\Object\SaleableInterface;
use Magento\Pricing\Price\Factory as PriceFactory;
use Magento\Pricing\Price\PriceInterface;

/**
 * Composite price model
 */
class PriceComposite
{
    /**
     * @var PriceFactory
     */
    protected $priceFactory;

    /**
     * @var array
     */
    protected $prices;

    /**
     * @param PriceFactory $priceFactory
     * @param array $prices
     */
    public function __construct(PriceFactory $priceFactory, array $prices = [])
    {
        $this->priceFactory = $priceFactory;
        $this->prices = $prices;
    }

    /**
     * @return array
     */
    public function getPriceCodes()
    {
        return array_keys($this->prices);
    }

    /**
     * @param SaleableInterface $salableItem
     * @param string $priceCode
     * @param float $quantity
     * @throws \InvalidArgumentException
     * @return PriceInterface
     */
    public function createPriceObject(SaleableInterface $salableItem, $priceCode, $quantity)
    {
        if (!isset($this->prices[$priceCode])) {
            throw new \InvalidArgumentException($priceCode . ' is not registered in prices list');
        }
        $className = $this->prices[$priceCode];
        return $this->priceFactory->create($salableItem, $className, $quantity);
    }
}
