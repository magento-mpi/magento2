<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Bundle Option Resource Model
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Bundle_Model_Resource_Option extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize connection and define resource
     *
     */
    protected function _construct()
    {
        $this->_init('catalog_product_bundle_option', 'option_id');
    }

    /**
     * After save process
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Bundle_Model_Resource_Option
     */
    protected function _afterSave(Magento_Core_Model_Abstract $object)
    {
        parent::_afterSave($object);

        $condition = array(
            'option_id = ?' => $object->getId(),
            'store_id = ? OR store_id = 0' => $object->getStoreId()
        );

        $write = $this->_getWriteAdapter();
        $write->delete($this->getTable('catalog_product_bundle_option_value'), $condition);

        $data = new \Magento\Object();
        $data->setOptionId($object->getId())
            ->setStoreId($object->getStoreId())
            ->setTitle($object->getTitle());

        $write->insert($this->getTable('catalog_product_bundle_option_value'), $data->getData());

        /**
         * also saving default value if this store view scope
         */

        if ($object->getStoreId()) {
            $data->setStoreId(0);
            $data->setTitle($object->getDefaultTitle());
            $write->insert($this->getTable('catalog_product_bundle_option_value'), $data->getData());
        }

        return $this;
    }

    /**
     * After delete process
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Bundle_Model_Resource_Option
     */
    protected function _afterDelete(Magento_Core_Model_Abstract $object)
    {
        parent::_afterDelete($object);

        $this->_getWriteAdapter()->delete(
            $this->getTable('catalog_product_bundle_option_value'),
            array('option_id = ?' => $object->getId())
        );

        return $this;
    }

    /**
     * Retrieve options searchable data
     *
     * @param int $productId
     * @param int $storeId
     * @return array
     */
    public function getSearchableData($productId, $storeId)
    {
        $adapter = $this->_getReadAdapter();

        $title = $adapter->getCheckSql('option_title_store.title IS NOT NULL',
            'option_title_store.title',
            'option_title_default.title'
        );
        $bind = array(
            'store_id'   => $storeId,
            'product_id' => $productId
        );
        $select = $adapter->select()
            ->from(array('opt' => $this->getMainTable()), array())
            ->join(
                array('option_title_default' => $this->getTable('catalog_product_bundle_option_value')),
                'option_title_default.option_id = opt.option_id AND option_title_default.store_id = 0',
                array()
            )
            ->joinLeft(
                array('option_title_store' => $this->getTable('catalog_product_bundle_option_value')),
                'option_title_store.option_id = opt.option_id AND option_title_store.store_id = :store_id',
                array('title' => $title)
            )
            ->where('opt.parent_id=:product_id');
        if (!$searchData = $adapter->fetchCol($select, $bind)) {
            $searchData = array();
        }
        return $searchData;
    }
}
