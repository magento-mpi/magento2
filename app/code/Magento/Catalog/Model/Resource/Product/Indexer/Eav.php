<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Product Eav Indexer Resource Model
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Resource_Product_Indexer_Eav extends Magento_Catalog_Model_Resource_Product_Indexer_Abstract
{
    /**
     * EAV Indexers by type
     *
     * @var array
     */
    protected $_types;

    /**
     * Define main index table
     *
     */
    protected function _construct()
    {
        $this->_init('catalog_product_index_eav', 'entity_id');
    }

    /**
     * Retrieve array of EAV type indexers
     *
     * @return array
     */
    public function getIndexers()
    {
        if (is_null($this->_types)) {
            $this->_types   = array(
                'source'    => Mage::getResourceModel('Magento_Catalog_Model_Resource_Product_Indexer_Eav_Source'),
                'decimal'   => Mage::getResourceModel('Magento_Catalog_Model_Resource_Product_Indexer_Eav_Decimal'),
            );
        }

        return $this->_types;
    }

    /**
     * Retrieve indexer instance by type
     *
     * @param string $type
     * @return Magento_Catalog_Model_Resource_Product_Indexer_Eav_Abstract
     */
    public function getIndexer($type)
    {
        $indexers = $this->getIndexers();
        if (!isset($indexers[$type])) {
            Mage::throwException(Mage::helper('Magento_Catalog_Helper_Data')->__('We found an unknown EAV indexer type "%s".', $type));
        }
        return $indexers[$type];
    }

    /**
     * Process product save.
     * Method is responsible for index support
     * when product was saved and assigned categories was changed.
     *
     * @param Magento_Index_Model_Event $event
     * @return Magento_Catalog_Model_Resource_Product_Indexer_Eav
     */
    public function catalogProductSave(Magento_Index_Model_Event $event)
    {
        $productId = $event->getEntityPk();
        $data = $event->getNewData();

        /**
         * Check if filterable attribute values were updated
         */
        if (!isset($data['reindex_eav'])) {
            return $this;
        }

        foreach ($this->getIndexers() as $indexer) {
            /** @var $indexer Magento_Catalog_Model_Resource_Product_Indexer_Eav_Abstract */
            $indexer->reindexEntities($productId);
        }

        return $this;
    }

    /**
     * Process Product Delete
     *
     * @param Magento_Index_Model_Event $event
     * @return Magento_Catalog_Model_Resource_Product_Indexer_Eav
     */
    public function catalogProductDelete(Magento_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (empty($data['reindex_eav_parent_ids'])) {
            return $this;
        }

        foreach ($this->getIndexers() as $indexer) {
            /** @var $indexer Magento_Catalog_Model_Resource_Product_Indexer_Eav_Abstract */
            $indexer->reindexEntities($data['reindex_eav_parent_ids']);
        }

        return $this;
    }

    /**
     * Process Product Mass Update
     *
     * @param Magento_Index_Model_Event $event
     * @return Magento_Catalog_Model_Resource_Product_Indexer_Eav
     */
    public function catalogProductMassAction(Magento_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (empty($data['reindex_eav_product_ids'])) {
            return $this;
        }

        foreach ($this->getIndexers() as $indexer) {
            /** @var $indexer Magento_Catalog_Model_Resource_Product_Indexer_Eav_Abstract */
            $indexer->reindexEntities($data['reindex_eav_product_ids']);
        }

        return $this;
    }

    /**
     * Process Catalog Eav Attribute Save
     *
     * @param Magento_Index_Model_Event $event
     * @return Magento_Catalog_Model_Resource_Product_Indexer_Eav
     */
    public function catalogEavAttributeSave(Magento_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (empty($data['reindex_attribute'])) {
            return $this;
        }

        $indexer = $this->getIndexer($data['attribute_index_type']);

        $indexer->reindexAttribute($event->getEntityPk(), !empty($data['is_indexable']));

        return $this;
    }

    /**
     * Rebuild all index data
     *
     * @return Magento_Catalog_Model_Resource_Product_Indexer_Eav
     */
    public function reindexAll()
    {
        $this->useIdxTable(true);
        foreach ($this->getIndexers() as $indexer) {
            /** @var $indexer Magento_Catalog_Model_Resource_Product_Indexer_Eav_Abstract */
            $indexer->reindexAll();
        }

        return $this;
    }

    /**
     * Retrieve temporary source index table name
     *
     * @param string $table
     * @return string
     */
    public function getIdxTable($table = null)
    {
        if ($this->useIdxTable()) {
           return $this->getTable('catalog_product_index_eav_idx');
        }
        return $this->getTable('catalog_product_index_eav_tmp');
    }
}
