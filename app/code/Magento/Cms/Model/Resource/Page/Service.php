<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cms page service resource model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Cms\Model\Resource\Page;

class Service extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Init cms page service model
     *
     */
    protected function _construct()
    {
        $this->_init('cms_page', 'page_id');
    }

    /**
     * Unlinks from $fromStoreId store pages that have same identifiers as pages in $byStoreId
     *
     * Routine is intented to be used before linking pages of some store ($byStoreId) to other store ($fromStoreId)
     * to prevent duplication of url keys
     *
     * Resolved $byLinkTable can be provided when restoring links from some backup table
     *
     * @param int $fromStoreId
     * @param int $byStoreId
     * @param string $byLinkTable
     *
     * @return \Magento\Cms\Model\Resource\Page\Service
     */
    public function unlinkConflicts($fromStoreId, $byStoreId, $byLinkTable = null)
    {
        $readAdapter = $this->_getReadAdapter();

        $linkTable = $this->getTable('cms_page_store');
        $mainTable = $this->getMainTable();
        $byLinkTable = $byLinkTable ? $byLinkTable : $linkTable;

        // Select all page ids of $fromStoreId that have identifiers as some pages in $byStoreId
        $select = $readAdapter->select()
            ->from(array('from_link' => $linkTable), 'page_id')
            ->join(
                array('from_entity' => $mainTable),
                $readAdapter->quoteInto(
                    'from_entity.page_id = from_link.page_id AND from_link.store_id = ?',
                    $fromStoreId
                ),
                array()
            )->join(
                array('by_entity' => $mainTable),
                'from_entity.identifier = by_entity.identifier AND from_entity.page_id != by_entity.page_id',
                array()
            )->join(
                array('by_link' => $byLinkTable),
                $readAdapter->quoteInto('by_link.page_id = by_entity.page_id AND by_link.store_id = ?', $byStoreId),
                array()
            );
        $pageIds = $readAdapter->fetchCol($select);

        // Unlink found pages
        if ($pageIds) {
            $writeAdapter = $this->_getWriteAdapter();
            $where = array(
                'page_id IN (?)'   => $pageIds,
                'AND store_id = ?' => $fromStoreId
            );
            $writeAdapter->delete($linkTable, $where);
        }
        return $this;
    }
}
