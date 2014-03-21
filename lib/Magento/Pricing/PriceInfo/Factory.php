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
namespace Magento\Pricing\PriceInfo;

use Magento\Pricing\Object\SaleableInterface;

/**
 * Price info model factory
 */
class Factory
{
    /**
     * Default Price Info class
     */
    const DEFAULT_PRICE_INFO_CLASS = 'Magento\Pricing\PriceInfoInterface';

    /**
     * List of Price Info classes by product types
     * @var array
     */
    protected $types = [];

    /**
     * Object Manager
     *
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * Construct
     *
     * @param array $types
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(array $types, \Magento\ObjectManager $objectManager)
    {
        $this->types = $types;
        $this->objectManager = $objectManager;
    }

    /**
     * Create Price Info object for particular product
     *
     * @param SaleableInterface $product
     * @param array $arguments
     * @return \Magento\Pricing\PriceInfoInterface
     * @throws \InvalidArgumentException
     */
    public function create(SaleableInterface $product, array $arguments = [])
    {
        $type = $product->getTypeId();
        $className = isset($this->types[$type]) ? $this->types[$type] : self::DEFAULT_PRICE_INFO_CLASS;

        $arguments['product'] = $product;
        $priceInfo = $this->objectManager->create($className, $arguments);

        if (!$priceInfo instanceof \Magento\Pricing\PriceInfoInterface) {
            throw new \InvalidArgumentException(
                $className . ' doesn\'t implement \Magento\Pricing\PriceInfoInterface'
            );
        }

        return $priceInfo;
    }
}
