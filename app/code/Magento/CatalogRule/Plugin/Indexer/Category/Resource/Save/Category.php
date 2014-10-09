<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogRule\Plugin\Indexer\Category\Resource\Save;

class Category
{
    protected $productProcessor;

    /**
     * @param \Magento\CatalogRule\Model\Indexer\Product\ProductProcessor $productProcessor
     */
    public function __construct(
        \Magento\CatalogRule\Model\Indexer\Product\ProductProcessor $productProcessor
    ) {
        $this->productProcessor = $productProcessor;
    }

    /**
     * @param \Magento\Catalog\Model\Resource\Category\Interceptor $subject
     * @param callable $proceed
     * @param \Magento\Framework\Model\AbstractModel $category
     * @return mixed
     */
    public function afterSave(
        \Magento\Framework\Model\AbstractModel $category
    ) {
        /** @var \Magento\Catalog\Model\Category $category */
        $productIds = $category->getAffectedProductIds();
        if ($productIds && !$this->productProcessor->getIndexer()->isScheduled()) {
            $this->productProcessor->reindexList($productIds);
        }
        return $category;
    }
}
