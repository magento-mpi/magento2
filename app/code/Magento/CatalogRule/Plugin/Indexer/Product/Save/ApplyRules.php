<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogRule\Plugin\Indexer\Product\Save;

use Magento\Catalog\Model\Product;
use Magento\CatalogRule\Model\Indexer\Product\ProductRuleProcessor;

class ApplyRules
{
    /**
     * @var ProductRuleProcessor
     */
    protected $productRuleProcessor;

    /**
     * @param ProductRuleProcessor $productRuleProcessor
     */
    public function __construct(ProductRuleProcessor $productRuleProcessor)
    {
        $this->productRuleProcessor = $productRuleProcessor;
    }

    /**
     * Apply catalog rules after product save
     *
     * @param Product $subject
     * @param Product $product
     * @return Product
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(
        Product $subject,
        Product $product
    ) {
        if (!$product->getIsMassupdate()) {
            $this->productRuleProcessor->reindexRow($product->getId());
        }
        return $product;
    }
}
