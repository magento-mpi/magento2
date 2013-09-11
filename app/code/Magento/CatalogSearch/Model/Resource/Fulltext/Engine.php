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
 * CatalogSearch Fulltext Index Engine resource model
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogSearch\Model\Resource\Fulltext;

class Engine extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Init resource model
     *
     */
    protected function _construct()
    {
        $this->_init('catalogsearch_fulltext', 'product_id');
    }

    /**
     * Add entity data to fulltext search table
     *
     * @param int $entityId
     * @param int $storeId
     * @param array $index
     * @param string $entity 'product'|'cms'
     * @return \Magento\CatalogSearch\Model\Resource\Fulltext\Engine
     */
    public function saveEntityIndex($entityId, $storeId, $index, $entity = 'product')
    {
        $this->_getWriteAdapter()->insert($this->getMainTable(), array(
            'product_id'    => $entityId,
            'store_id'      => $storeId,
            'data_index'    => $index
        ));
        return $this;
    }

    /**
     * Multi add entities data to fulltext search table
     *
     * @param int $storeId
     * @param array $entityIndexes
     * @param string $entity 'product'|'cms'
     * @return \Magento\CatalogSearch\Model\Resource\Fulltext\Engine
     */
    public function saveEntityIndexes($storeId, $entityIndexes, $entity = 'product')
    {
        $data    = array();
        $storeId = (int)$storeId;
        foreach ($entityIndexes as $entityId => $index) {
            $data[] = array(
                'product_id'    => (int)$entityId,
                'store_id'      => $storeId,
                'data_index'    => $index
            );
        }

        if ($data) {
            \Mage::getResourceHelper('Magento_CatalogSearch')
                ->insertOnDuplicate($this->getMainTable(), $data, array('data_index'));
        }

        return $this;
    }

    /**
     * Retrieve allowed visibility values for current engine
     *
     * @return array
     */
    public function getAllowedVisibility()
    {
        return \Mage::getSingleton('Magento\Catalog\Model\Product\Visibility')->getVisibleInSearchIds();
    }

    /**
     * Define if current search engine supports advanced index
     *
     * @return bool
     */
    public function allowAdvancedIndex()
    {
        return false;
    }

    /**
     * Remove entity data from fulltext search table
     *
     * @param int $storeId
     * @param int $entityId
     * @param string $entity 'product'|'cms'
     * @return \Magento\CatalogSearch\Model\Resource\Fulltext\Engine
     */
    public function cleanIndex($storeId = null, $entityId = null, $entity = 'product')
    {
        $where = array();

        if (!is_null($storeId)) {
            $where[] = $this->_getWriteAdapter()->quoteInto('store_id=?', $storeId);
        }
        if (!is_null($entityId)) {
            $where[] = $this->_getWriteAdapter()->quoteInto('product_id IN (?)', $entityId);
        }

        // Delete locks reading queries and causes performance issues
        // Insert into index goes with ON_DUPLICATE options.
        // Insert into catalogsearch_result goes with catalog_product_entity inner join
        //$this->_getWriteAdapter()->delete($this->getMainTable(), $where);

        return $this;
    }

    /**
     * Prepare index array as a string glued by separator
     *
     * @param array $index
     * @param string $separator
     * @return string
     */
    public function prepareEntityIndex($index, $separator = ' ')
    {
        return \Mage::helper('Magento\CatalogSearch\Helper\Data')->prepareIndexdata($index, $separator);
    }

    /**
     * Return resource name for the full text search
     *
     * @return null
     */
    public function getResourceName()
    {
        return '\Magento\CatalogSearch\Model\Resource\Advanced';
    }

    /**
     * Retrieve fulltext search result data collection
     *
     * @return \Magento\CatalogSearch\Model\Resource\Fulltext\Collection
     */
    public function getResultCollection()
    {
        return \Mage::getResourceModel('\Magento\CatalogSearch\Model\Resource\Fulltext\Collection');
    }

    /**
     * Retrieve advanced search result data collection
     *
     * @return \Magento\CatalogSearch\Model\Resource\Advanced\Collection
     */
    public function getAdvancedResultCollection()
    {
        return \Mage::getResourceModel('\Magento\CatalogSearch\Model\Resource\Advanced\Collection');
    }

    /**
     * Define if Layered Navigation is allowed
     *
     * @return bool
     */
    public function isLayeredNavigationAllowed()
    {
        return true;
    }

    /**
     * Define if engine is available
     *
     * @return bool
     */
    public function test()
    {
        return true;
    }
}
