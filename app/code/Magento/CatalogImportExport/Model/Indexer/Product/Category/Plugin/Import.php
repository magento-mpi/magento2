<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogImportExport\Model\Indexer\Product\Category\Plugin;

class Import
{
    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Category\Processor
     */
    protected $_indexerEavProcessor;

    /**
     * @param \Magento\Catalog\Model\Indexer\Product\Category\Processor $indexerEavProcessor
     */
    public function __construct(\Magento\Catalog\Model\Indexer\Product\Category\Processor $indexerEavProcessor)
    {
        $this->_indexerEavProcessor = $indexerEavProcessor;
    }

    /**
     * After import handler
     *
     * @param \Magento\ImportExport\Model\Import $subject
     * @param Object $import
     *
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterImportSource(\Magento\ImportExport\Model\Import $subject, $import)
    {
        $this->_indexerEavProcessor->markIndexerAsInvalid();
        return $import;
    }
}
