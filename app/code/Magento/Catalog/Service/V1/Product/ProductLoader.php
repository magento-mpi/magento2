<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\Catalog\Service\V1\Product;

use Magento\Framework\Exception\NoSuchEntityException;

class ProductLoader
{
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     */
    public function __construct(\Magento\Catalog\Model\ProductFactory $productFactory)
    {
        $this->productFactory = $productFactory;
    }

    /**
     * Load product by SKU
     *
     * @param  string $productSku
     * @return \Magento\Catalog\Model\Product
     * @throws NoSuchEntityException
     */
    public function load($productSku)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->productFactory->create();
        $productId = $product->getIdBySku($productSku);

        if (!$productId) {
            throw new NoSuchEntityException('There is no product with provided SKU');
        }
        $product->load($productId);
        return $product;
    }
}
