<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Model\Resource\Indexer;

/**
 * CatalogSearch fulltext indexer resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Fulltext extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Initialize connection and define catalog product table as main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('catalogsearch_fulltext', 'product_id');
    }

    /**
     * Retrieve product relations by children
     *
     * @param int|array $childIds
     * @return array
     */
    public function getRelationsByChild($childIds)
    {
        $write = $this->_getWriteAdapter();
        $select = $write->select()->from(
            $this->getTable('catalog_product_relation'),
            'parent_id'
        )->where(
            'child_id IN(?)',
            $childIds
        );

        return $write->fetchCol($select);
    }
}
