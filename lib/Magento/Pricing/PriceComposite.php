<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing;

use Magento\Pricing\Object\SaleableInterface;
use Magento\Pricing\Price\Factory as PriceFactory;
use Magento\Pricing\Price\PriceInterface;

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
     * @param string $priceCode
     * @param SaleableInterface $object
     * @return PriceInterface
     * @throws \InvalidArgumentException
     */
    public function createPriceObject($priceCode, SaleableInterface $object)
    {
        if (!isset($this->prices[$priceCode])) {
            throw new \InvalidArgumentException($priceCode . ' is not registered in prices list');
        }
        $className = $this->prices[$priceCode];
        return $this->priceFactory->create($className, $object);
    }
}
