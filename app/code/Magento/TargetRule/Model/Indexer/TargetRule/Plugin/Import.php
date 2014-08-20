<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TargetRule\Model\Indexer\TargetRule\Plugin;

use \Magento\ImportExport\Model\Import as ImportModel;

class Import extends AbstractPlugin
{
    /**
     * Invalidate target rule indexer
     *
     * @param ImportModel $subject
     * @param bool $result
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterImportSource(ImportModel $subject, $result)
    {
        $this->invalidateIndexers();
        return $result;
    }
}
