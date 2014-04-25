<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Pricing\Price;

use Magento\Framework\Pricing\Object\SaleableInterface;

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
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create Price object for particular product
     *
     * @param SaleableInterface $saleableItem
     * @param string $className
     * @param float $quantity
     * @param array $arguments
     * @throws \InvalidArgumentException
     * @return \Magento\Framework\Pricing\Price\PriceInterface
     */
    public function create(SaleableInterface $saleableItem, $className, $quantity, array $arguments = [])
    {
        $arguments['saleableItem'] = $saleableItem;
        $arguments['quantity'] = $quantity;
        $price = $this->objectManager->create($className, $arguments);
        if (!$price instanceof PriceInterface) {
            throw new \InvalidArgumentException(
                $className . ' doesn\'t implement \Magento\Framework\Pricing\Price\PriceInterface'
            );
        }
        return $price;
    }
}
