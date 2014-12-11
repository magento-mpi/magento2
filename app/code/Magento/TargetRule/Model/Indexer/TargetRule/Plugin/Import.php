<?php
/**
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\TargetRule\Model\Indexer\TargetRule\Plugin;

use Magento\ImportExport\Model\Import as ImportModel;

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
