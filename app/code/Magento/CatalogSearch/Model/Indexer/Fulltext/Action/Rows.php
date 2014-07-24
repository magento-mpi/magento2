<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Model\Indexer\Fulltext\Action;

class Rows extends \Magento\CatalogSearch\Model\Indexer\Fulltext\Action\Full
{
    /**
     * Refresh entities index
     *
     * @param int[] $entityIds
     * @param bool $useTempTable
     * @return void
     */
    public function reindex(array $entityIds = array(), $useTempTable = false)
    {
        $this->rebuildIndex(null, $entityIds);
    }
}
