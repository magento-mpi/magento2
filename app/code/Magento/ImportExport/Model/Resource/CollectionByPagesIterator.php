<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ImportExport\Model\Resource;

/**
 * Collection by pages iterator
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class CollectionByPagesIterator
{
    /**
     * Load collection page by page and apply callbacks to each collection item
     *
     * @param \Magento\Data\Collection\Db $collection Collection to load page by page
     * @param int $pageSize Number of items to fetch from db in one query
     * @param array $callbacks Array of callbacks which should be applied to each collection item
     * @return void
     */
    public function iterate(\Magento\Data\Collection\Db $collection, $pageSize, array $callbacks)
    {
        /** @var $paginatedCollection \Magento\Data\Collection\Db */
        $paginatedCollection = null;
        $pageNumber = 1;
        do {
            $paginatedCollection = clone $collection;
            $paginatedCollection->clear();

            $paginatedCollection->setPageSize($pageSize)->setCurPage($pageNumber);

            if ($paginatedCollection->count() > 0) {
                foreach ($paginatedCollection as $item) {
                    foreach ($callbacks as $callback) {
                        call_user_func($callback, $item);
                    }
                }
            }

            $pageNumber++;
        } while ($pageNumber <= $paginatedCollection->getLastPageNumber());

        $paginatedCollection->clear();
        unset($paginatedCollection);
    }
}
