<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog search query resource model
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogSearch_Model_Resource_Query extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Init resource data
     */
    protected function _construct()
    {
        $this->_init('catalogsearch_query', 'query_id');
    }

    /**
     * Custom load model by search query string
     *
     * @param Mage_Core_Model_Abstract $object
     * @param string $value
     * @return Mage_CatalogSearch_Model_Resource_Query
     */
    public function loadByQuery(Mage_Core_Model_Abstract $object, $value)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where('synonym_for=? OR query_text=?', $value)
            ->where('store_id=?', $object->getStoreId())
            ->order('synonym_for ASC')
            ->limit(1);
        $data = $this->_getReadAdapter()->fetchRow($select);
        if ($data) {
            $object->setData($data);
            $this->_afterLoad($object);
        }

        return $this;
    }

    /**
     * Custom load model only by query text (skip synonym for)
     *
     * @param Mage_Core_Model_Abstract $object
     * @param string $value
     * @return Mage_CatalogSearch_Model_Resource_Query
     */
    public function loadByQueryText(Mage_Core_Model_Abstract $object, $value)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where('query_text = ?', $value)
            ->where('store_id = ?', $object->getStoreId())
            ->limit(1);
        $data = $this->_getReadAdapter()->fetchRow($select);
        if ($data) {
            $object->setData($data);
            $this->_afterLoad($object);
        }
        return $this;
    }

    /**
     * Loading string as a value or regular numeric
     *
     * @param Mage_Core_Model_Abstract $object
     * @param int|string $value
     * @param null|string $field
     * @return Mage_CatalogSearch_Model_Resource_Query
     */
    public function load(Mage_Core_Model_Abstract $object, $value, $field = null)
    {
        if (is_numeric($value)) {
            return parent::load($object, $value);
        } else {
            $this->loadByQuery($object, $value);
        }
        return $this;
    }

    /**
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_CatalogSearch_Model_Resource_Query
     */
    public function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $object->setUpdatedAt($this->formatDate(Mage::getModel('Mage_Core_Model_Date')->gmtTimestamp()));
        return $this;
    }
}
