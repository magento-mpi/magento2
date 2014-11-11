<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogPermissions\Model\Indexer\Plugin;

class Import
{
    /** @var \Magento\Indexer\Model\IndexerRegistry */
    protected $indexerRegistry;

    /**
     * @var \Magento\CatalogPermissions\App\ConfigInterface
     */
    protected $config;

    /**
     * @param \Magento\CatalogPermissions\App\ConfigInterface $config
     * @param \Magento\Indexer\Model\IndexerRegistry $indexerRegistry
     */
    public function __construct(
        \Magento\CatalogPermissions\App\ConfigInterface $config,
        \Magento\Indexer\Model\IndexerRegistry $indexerRegistry
    ) {
        $this->config = $config;
        $this->indexerRegistry = $indexerRegistry;
    }

    /**
     * After import handler
     *
     * @param \Magento\ImportExport\Model\Import $subject
     * @param bool $import
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterImportSource(\Magento\ImportExport\Model\Import $subject, $import)
    {
        if ($this->config->isEnabled()) {
            $this->indexerRegistry->get(\Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID)->invalidate();
            $this->indexerRegistry->get(\Magento\CatalogPermissions\Model\Indexer\Product::INDEXER_ID)->invalidate();
        }
        return $import;
    }
}
