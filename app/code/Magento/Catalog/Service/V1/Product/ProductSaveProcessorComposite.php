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
 * Composite pattern implementation for ProductSaveProcessorInterface.
 *
 * Allows multiple savers to be registered and used for product data modification.
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
     * @param array $saveProcessors Array of the processors which should be registered in the following format:
     * <pre>
     * [
     *      ['type' => $firstProcessorObject, 'sortOrder' => 15],
     *      ['type' => $secondProcessorObject, 'sortOrder' => 10],
     *      ...
     * ]
     * </pre>
     */
    public function __construct(CompositeHelper $compositeHelper, $saveProcessors = [])
    {
        $saveProcessors = $compositeHelper->filterAndSortDeclaredComponents($saveProcessors);
        foreach ($saveProcessors as $saveProcessor) {
            $this->productSaveProcessors[] = $saveProcessor['type'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function create(
        \Magento\Catalog\Model\Product $product,
        \Magento\Catalog\Service\V1\Data\Product $productData
    ) {
        foreach ($this->productSaveProcessors as $saveProcessor) {
            $saveProcessor->create($product, $productData);
        }
        return $productData->getSku();
    }

    /**
     * {@inheritdoc}
     */
    public function afterCreate(
        \Magento\Catalog\Model\Product $product,
        \Magento\Catalog\Service\V1\Data\Product $productData
    ) {
        foreach ($this->productSaveProcessors as $saveProcessor) {
            $saveProcessor->afterCreate($product, $productData);
        }
        return $productData->getSku();
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
    }
}
