<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogRule\Plugin\Indexer;

use Magento\CatalogRule\Model\Indexer\Product\ProductRuleProcessor;

class Category
{
    /**
     * @var ProductRuleProcessor
     */
    protected $productRuleProcessor;

    /**
     * @param ProductRuleProcessor $productRuleProcessor
     */
    public function __construct(
        ProductRuleProcessor $productRuleProcessor
    ) {
        $this->productRuleProcessor = $productRuleProcessor;
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     * @return \Magento\Catalog\Model\Category
     */
    public function afterSave(
        \Magento\Catalog\Model\Category $category
    ) {
        /** @var \Magento\Catalog\Model\Category $category */
        $productIds = $category->getAffectedProductIds();
        if ($productIds && !$this->productRuleProcessor->getIndexer()->isScheduled()) {
            $this->productRuleProcessor->reindexList($productIds);
        }
        return $category;
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     * @return \Magento\Catalog\Model\Category
     */
    public function afterDelete(\Magento\Catalog\Model\Category $category)
    {
        $this->productRuleProcessor->markIndexerAsInvalid();
        return $category;
    }
}
