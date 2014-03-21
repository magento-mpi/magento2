<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing\Price;

use Magento\Pricing\Object\SaleableInterface;

/**
 * Price factory
 */
class Factory
{
    /**
     * Object Manager
     *
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * Construct
     *
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create Price object for particular product
     *
     * @param string $className
     * @param SaleableInterface $product
     * @param array $arguments
     * @return \Magento\Pricing\Price\PriceInterface
     * @throws \InvalidArgumentException
     */
    public function create($className, SaleableInterface $product, array $arguments = [])
    {
        $arguments['product'] = $product;
        $price = $this->objectManager->create($className, $arguments);
        if (!$price instanceof \Magento\Pricing\Price\PriceInterface) {
            throw new \InvalidArgumentException(
                $className . ' doesn\'t implement \Magento\Pricing\Price\PriceInterface'
            );
        }
        return $price;
    }
}
