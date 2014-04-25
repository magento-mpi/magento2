<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Price Info factory
 */
namespace Magento\Framework\Pricing\PriceInfo;

use Magento\Framework\Pricing\Object\SaleableInterface;

/**
 * Price info model factory
 */
class Factory
{
    /**
     * Default Price Info class
     */
    const DEFAULT_PRICE_INFO_CLASS = 'Magento\Framework\Pricing\PriceInfoInterface';

    /**
     * List of Price Info classes by product types
     *
     * @var array
     */
    protected $types = [];

    /**
     * Object Manager
     *
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    /**
     * Construct
     *
     * @param array $types
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(array $types, \Magento\Framework\ObjectManager $objectManager)
    {
        $this->types = $types;
        $this->objectManager = $objectManager;
    }

    /**
     * Create Price Info object for particular product
     *
     * @param SaleableInterface $saleableItem
     * @param array $arguments
     * @return \Magento\Framework\Pricing\PriceInfoInterface
     * @throws \InvalidArgumentException
     */
    public function create(SaleableInterface $saleableItem, array $arguments = [])
    {
        $type = $saleableItem->getTypeId();
        $className = isset($this->types[$type]) ? $this->types[$type] : self::DEFAULT_PRICE_INFO_CLASS;

        $arguments['saleableItem'] = $saleableItem;
        if ($saleableItem->getQty()) {
            $arguments['quantity'] = $saleableItem->getQty();
        }
        $priceInfo = $this->objectManager->create($className, $arguments);

        if (!$priceInfo instanceof \Magento\Framework\Pricing\PriceInfoInterface) {
            throw new \InvalidArgumentException(
                $className . ' doesn\'t implement \Magento\Framework\Pricing\PriceInfoInterface'
            );
        }
        return $priceInfo;
    }
}
