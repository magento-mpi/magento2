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
    protected $metadata;

    /**
     * @param PriceFactory $priceFactory
     * @param array $metadata
     */
    public function __construct(PriceFactory $priceFactory, array $metadata = [])
    {
        $this->priceFactory = $priceFactory;
        $this->metadata = $metadata;
    }

    /**
     * @return array
     */
    public function getPriceCodes()
    {
        return array_keys($this->metadata);
    }

    /**
     * Returns metadata for prices
     *
     * @return array
     */
    public function getMetadata()
    {
        return $this->metadata;
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
        if (!isset($this->metadata[$priceCode])) {
            throw new \InvalidArgumentException($priceCode . ' is not registered in prices list');
        }
        $className = $this->metadata[$priceCode]['class'];
        return $this->priceFactory->create($salableItem, $className, $quantity);
    }
}
