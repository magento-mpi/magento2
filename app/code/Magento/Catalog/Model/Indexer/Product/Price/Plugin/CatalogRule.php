<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Product\Price\Plugin;

class CatalogRule
{
    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Price\Processor
     */
    protected $_processor;

    /**
     * @param \Magento\Catalog\Model\Indexer\Product\Price\Processor $processor
     */
    public function __construct(
        \Magento\Catalog\Model\Indexer\Product\Price\Processor $processor
    ) {
        $this->_processor = $processor;
    }

    /**
     * Invalidate price indexer
     *
     * @param \Magento\CatalogRule\Model\Rule $subject
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterApplyAll(\Magento\CatalogRule\Model\Rule $subject)
    {
        $this->_processor->markIndexerAsInvalid();
    }

    /**
     * Reindex price for affected product
     *
     * @param \Magento\CatalogRule\Model\Rule $subject
     * @param callable $proceed
     * @param int|\Magento\Catalog\Model\Product $product
     * @param null|array $websiteIds
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
     */
    protected function _reindexProduct($product)
    {
        $productId = is_numeric($product) ? $product : $product->getId();
        $this->_processor->reindexRow($productId);
    }
}
