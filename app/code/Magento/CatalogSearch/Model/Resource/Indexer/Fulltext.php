<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * CatalogSearch fulltext indexer resource model
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogSearch\Model\Resource\Indexer;

class Fulltext extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Initialize connection and define catalog product table as main table
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
        $select = $write->select()
            ->from($this->getTable('catalog_product_relation'), 'parent_id')
            ->where('child_id IN(?)', $childIds);

        return $write->fetchCol($select);
    }
}
