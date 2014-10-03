<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogRule\Model\Indexer\Product\Plugin\Save;

use Magento\Catalog\Model\Product;
use Magento\CatalogRule\Model\Indexer\ProductRuleIndexer;

class ApplyRules
{
    /**
     * @var ProductRuleIndexer
     */
    protected $productRuleIndexer;

    /**
     * @param ProductRuleIndexer $productRuleIndexer
     */
    public function __construct(
        ProductRuleIndexer $productRuleIndexer
    ) {
        $this->productRuleIndexer = $productRuleIndexer;
    }

    /**
     * Apply catalog rules after product save
     *
     * @param Product $product
     * @return Product
     */
    public function afterSave(Product $product)
    {
        // TODO: check on save or schedule
        if (!$product->getIsMassupdate()) {
            $this->productRuleIndexer->executeRow($product->getId());
        }
        return $product;
    }
}
