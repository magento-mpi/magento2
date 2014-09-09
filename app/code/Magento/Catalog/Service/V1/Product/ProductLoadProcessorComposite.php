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
 * Composite loader of product data.
 *
 * Allows multiple loaders to be registered and used during product data loading.
 */
class ProductLoadProcessorComposite implements ProductLoadProcessorInterface
{
    /**
     * @var ProductLoadProcessorInterface[]
     */
    protected $productLoadProcessors = [];

    /**
     * Register product load processors.
     *
     * @param CompositeHelper $compositeHelper
     * @param array $loadProcessors Array of the processors which should be registered in the following format:
     * <pre>
     * [
     *      ['type' => $firstProcessorObject, 'sortOrder' => 15],
     *      ['type' => $secondProcessorObject, 'sortOrder' => 10],
     *      ...
     * ]
     * </pre>
     */
    public function __construct(CompositeHelper $compositeHelper, $loadProcessors = [])
    {
        $loadProcessors = $compositeHelper->filterAndSortDeclaredComponents($loadProcessors);
        foreach ($loadProcessors as $loadProcessor) {
            $this->productLoadProcessors[] = $loadProcessor['type'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function load($id, \Magento\Catalog\Service\V1\Data\ProductBuilder $productBuilder)
    {
        foreach ($this->productLoadProcessors as $loadProcessor) {
            $loadProcessor->load($id, $productBuilder);
        }
    }
}
