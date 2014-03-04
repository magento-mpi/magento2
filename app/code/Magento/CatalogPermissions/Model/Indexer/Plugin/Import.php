<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogPermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogPermissions\Model\Indexer\Plugin;

class Import
{
    /**
     * @var \Magento\CatalogPermissions\Model\Indexer\Product
     */
    protected $productIndexer;

    /**
     * @var \Magento\CatalogPermissions\Model\Indexer\Category
     */
    protected $categoryIndexer;

    /**
     * @param \Magento\CatalogPermissions\Model\Indexer\Product $productIndexer
     * @param \Magento\CatalogPermissions\Model\Indexer\Category $categoryIndexer
     */
    public function __construct(
        \Magento\CatalogPermissions\Model\Indexer\Product $productIndexer,
        \Magento\CatalogPermissions\Model\Indexer\Category $categoryIndexer
    ) {
        $this->productIndexer = $productIndexer;
        $this->categoryIndexer = $categoryIndexer;
    }

    /**
     * After improt handler
     *
     * @param \Magento\ImportExport\Model\Import $subject
     * @param Object $import
     *
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterImportSource(\Magento\ImportExport\Model\Import $subject, $import)
    {
        $this->productIndexer->markIndexerAsInvalid();
        $this->categoryIndexer->markIndexerAsInvalid();
        return $import;
    }
}
