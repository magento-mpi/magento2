<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogImportExport\Model\Indexer\Product\Price\Plugin;

class Import extends \Magento\Catalog\Model\Indexer\Product\Price\Plugin\AbstractPlugin
{
    /**
     * After import handler
     *
     * @param \Magento\ImportExport\Model\Import $subject
     * @param bool $result
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterImportSource(\Magento\ImportExport\Model\Import $subject, $result)
    {
        $this->invalidateIndexer();
        return $result;
    }
}
