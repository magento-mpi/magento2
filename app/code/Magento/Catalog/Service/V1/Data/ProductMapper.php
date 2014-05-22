<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data;

class ProductMapper
{
    /** @var  \Magento\Catalog\Model\ProductFactory */
    protected $productFactory;

    public function __construct(\Magento\Catalog\Model\ProductFactory $productFactory)
    {
        $this->productFactory = $productFactory;
    }

    /**
     * @param Product $product
     * @return \Magento\Catalog\Model\Product
     */
    public function toModel(Product $product)
    {
        /** @var \Magento\Catalog\Model\Product $productModel */
        $productModel = $this->productFactory->create();
        $productModel->setData($product->__toArray());
        return $productModel;
    }
}