<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Plugin;

class Import extends AbstractPlugin
{
    /**
     * Invalidate target rule indexer
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
