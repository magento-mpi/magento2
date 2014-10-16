<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogRule\Plugin\Indexer;

class CatalogRule
{
    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Price\Processor
     */
    protected $processor;

    /**
     * @param \Magento\Catalog\Model\Indexer\Product\Price\Processor $processor
     */
    public function __construct(
        \Magento\Catalog\Model\Indexer\Product\Price\Processor $processor
    ) {
        $this->processor = $processor;
    }

    /**
     * Reindex price for affected product
     *
     * @param \Magento\CatalogRule\Model\Rule $subject
     * @param callable $proceed
     * @param int|\Magento\Catalog\Model\Product $product
     * @param null|array $websiteIds
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundApplyToProduct(
        \Magento\CatalogRule\Model\Rule $subject,
        \Closure $proceed,
        $product,
        $websiteIds = null
    ) {
        $proceed($product, $websiteIds);
        $this->_reindexProduct($product);
    }

    /**
     * Reindex price for affected product
     *
     * @param \Magento\CatalogRule\Model\Rule $subject
     * @param callable $proceed
     * @param int|\Magento\Catalog\Model\Product $product
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundApplyAllRulesToProduct(
        \Magento\CatalogRule\Model\Rule $subject,
        \Closure $proceed,
        $product
    ) {
        $proceed($product);
        $this->_reindexProduct($product);
    }

    /**
     * Reindex product price
     *
     * @param int|\Magento\Catalog\Model\Product $product
     *
     * @return void
     */
    protected function _reindexProduct($product)
    {
        $productId = is_numeric($product) ? $product : $product->getId();
        $this->processor->reindexRow($productId);
    }
}
