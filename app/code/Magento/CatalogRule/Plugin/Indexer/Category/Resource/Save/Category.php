<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogRule\Plugin\Indexer\Category\Resource\Save;

use Magento\CatalogRule\Model\Indexer\ProductRuleProcessor;

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
     * @param \Magento\Framework\Model\AbstractModel $category
     * @internal param callable $proceed
     * @return \Magento\Catalog\Model\Category
     */
    public function afterSave(
        \Magento\Framework\Model\AbstractModel $category
    ) {
        /** @var \Magento\Catalog\Model\Category $category */
        $productIds = $category->getAffectedProductIds();
        if ($productIds && !$this->productRuleProcessor->getIndexer()->isScheduled()) {
            $this->productRuleProcessor->reindexList($productIds);
        }
        return $category;
    }
}
