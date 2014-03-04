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
     * @var \Magento\Indexer\Model\IndexerFactory
     */
    protected $indexerFactory;

    /**
     * @var \Magento\CatalogPermissions\App\ConfigInterface
     */
    protected $config;

    /**
     * @param \Magento\CatalogPermissions\App\ConfigInterface $config
     * @param \Magento\Indexer\Model\IndexerFactory $indexerFactory
     */
    public function __construct(
        \Magento\CatalogPermissions\App\ConfigInterface $config,
        \Magento\Indexer\Model\IndexerFactory $indexerFactory
    ) {
        $this->config = $config;
        $this->indexerFactory = $indexerFactory;
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
        if (!$this->config->isEnabled()) {
            return;
        }

        $categoryIndex = $this->indexerFactory->create()
            ->load(\Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID);
        $categoryIndex->invalidate();

        $productIndex = $this->indexerFactory->create()
            ->load(\Magento\CatalogPermissions\Model\Indexer\Product::INDEXER_ID);
        $productIndex->invalidate();

        return $import;
    }
}
