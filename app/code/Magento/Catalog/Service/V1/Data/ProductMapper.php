<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data;

use \Magento\Framework\Service\EavDataObjectConverter;

class ProductMapper
{
    /** @var  \Magento\Catalog\Model\ProductFactory */
    protected $productFactory;

    /**
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     */
    public function __construct(\Magento\Catalog\Model\ProductFactory $productFactory)
    {
        $this->productFactory = $productFactory;
    }

    /**
     * @param Product $product
     * @return \Magento\Catalog\Model\Product
     */
    public function toModel(
        Product $product,
        $productModel = null
    ) {
        /** @var \Magento\Catalog\Model\Product $productModel */
        $productModel = $productModel ?: $this->productFactory->create();
        $productModel->addData(EavDataObjectConverter::toFlatArray($product));
        if (!$productModel->hasAttributeSetId()) {
            $productModel->setAttributeSetId($productModel->getDefaultAttributeSetId());
        }
        if (!$productModel->hasTypeId()) {
            $productModel->setTypeId('simple');
        }
        return $productModel;
    }
}