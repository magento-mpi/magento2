<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product;

/**
 * Price model for external catalogs
 */
class CatalogPrice implements CatalogPriceInterface
{
    /**
     * @var \Magento\App\ObjectManager
     */
    protected $objectManager;

    /**
     * @var array catalog price models for different product types
     */
    protected $typePool;

    /**
     *
     * @param \Magento\App\ObjectManager $objectManager
     * @param array $typePool
     */
    public function __construct(
        \Magento\App\ObjectManager $objectManager,
        array $typePool
    ) {
        $this->objectManager = $objectManager;
        $this->typePool = $typePool;
    }

    /**
     * Minimal price for "regular" user
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param null|\Magento\Core\Model\Store $store Store view
     * @param \bool $inclTax
     * @return null|float
     */
    public function getCatalogPrice(\Magento\Catalog\Model\Product $product, $store = null, \bool $inclTax = false)
    {
        if (array_key_exists($product->getTypeId(), $this->typePool)) {
            $catalogPriceModel = $this->objectManager->get($this->typePool[$product->getTypeId()]);
            return $catalogPriceModel->getCatalogPrice($product, $store, $inclTax);
        }

        return $product->getFinalPrice();
    }


    /**
     * Regular catalog price
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return null
     */
    public function getCatalogRegularPrice(\Magento\Catalog\Model\Product $product)
    {
        if (array_key_exists($product->getTypeId(), $this->typePool)) {
            $catalogPriceModel = $this->objectManager->get(${$this->typePool[$product->getTypeId()]});
            return $catalogPriceModel->getCatalogRegularPrice($product);
        }

        return $product->getPrice();
    }

}