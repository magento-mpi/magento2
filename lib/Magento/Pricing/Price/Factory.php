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
     * @param SaleableInterface $salableItem
     * @param string $className
     * @param float $quantity
     * @param array $arguments
     * @throws \InvalidArgumentException
     * @return \Magento\Pricing\Price\PriceInterface
     */
    public function create(SaleableInterface $salableItem, $className, $quantity, array $arguments = [])
    {
        $arguments['salableItem'] = $salableItem;
        $arguments['quantity'] = $quantity;
        $price = $this->objectManager->create($className, $arguments);
        if (!$price instanceof PriceInterface) {
            throw new \InvalidArgumentException(
                $className . ' doesn\'t implement \Magento\Pricing\Price\PriceInterface'
            );
        }
        return $price;
    }
}
