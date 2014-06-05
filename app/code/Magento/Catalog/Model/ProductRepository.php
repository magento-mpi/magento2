<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model;

use Magento\Framework\Exception\NoSuchEntityException;

class ProductRepository
{
    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var Product[]
     */
    protected $instances = array();

    /**
     * @param ProductFactory $productFactory
     */
    public function __construct(ProductFactory $productFactory)
    {
        $this->productFactory = $productFactory;
    }

    /**
     * Retrieve product instance by sku
     *
     * @param string $sku
     * @return Product
     * @throws NoSuchEntityException
     */
    public function get($sku)
    {
        if (!isset($this->instances[$sku])) {
            $product = $this->productFactory->create();
            $productId = $product->getIdBySku($sku);
            if (!$productId) {
                throw new NoSuchEntityException();
            }
            $product->load($productId);
            $this->instances[$sku] = $product;
        }
        return $this->instances[$sku];
    }
}
