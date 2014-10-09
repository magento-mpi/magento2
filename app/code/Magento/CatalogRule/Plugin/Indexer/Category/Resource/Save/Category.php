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
    public function aroundSave(
        \Magento\Framework\Model\Resource\AbstractResource $subject,
        \Closure $proceed,
        \Magento\Framework\Model\AbstractModel $category
    ) {
        /** @var \Magento\Catalog\Model\Category $category */
        $productIds = array_keys($category->getPostedProducts());
        $result = $proceed($category);
        if ($productIds && !$this->productProcessor->getIndexer()->isScheduled()) {
            $this->productProcessor->reindexList($productIds);
        }
        return $result;
    }
}
