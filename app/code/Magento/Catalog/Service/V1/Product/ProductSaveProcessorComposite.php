<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product;

use Magento\Framework\ObjectManager\Helper\Composite as CompositeHelper;

/**
 * Composite processor of product data which allows modifications.
 *
 * Allows multiple savers to be registered and used during product data modification.
 */
class ProductSaveProcessorComposite implements ProductSaveProcessorInterface
{
    /**
     * @var ProductSaveProcessorInterface[]
     */
    protected $productSaveProcessors = [];

    /**
     * Register product save processors.
     *
     * @param CompositeHelper $compositeHelper
     * @param ProductSaveProcessorInterface[] $saveProcessors
     */
    public function __construct(CompositeHelper $compositeHelper, $saveProcessors = [])
    {
        $saveProcessors = $compositeHelper->filterAndSortDeclaredComponents($saveProcessors);
        foreach ($saveProcessors as $saveProcessor) {
            $this->productSaveProcessors = $saveProcessor['type'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function create(\Magento\Catalog\Service\V1\Data\Product $product)
    {
        foreach ($this->productSaveProcessors as $saveProcessor) {
            $saveProcessor->create($product);
        }
        return $product->getSku();
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, \Magento\Catalog\Service\V1\Data\Product $product)
    {
        foreach ($this->productSaveProcessors as $saveProcessor) {
            $saveProcessor->update($id, $product);
        }
        return $product->getSku();
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Magento\Catalog\Service\V1\Data\Product $product)
    {
        foreach ($this->productSaveProcessors as $saveProcessor) {
            $saveProcessor->delete($product);
        }
        return true;
    }
}
