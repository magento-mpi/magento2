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
 * Catalog search query resource model
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CatalogSearch_Model_Resource_Query extends Magento_Core_Model_Resource_Db_Abstract
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
     * @param Magento_Core_Model_Abstract $object
     * @param string $value
     * @return Magento_CatalogSearch_Model_Resource_Query
     */
    public function loadByQuery(Magento_Core_Model_Abstract $object, $value)
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
     * @param Magento_Core_Model_Abstract $object
     * @param string $value
     * @return Magento_CatalogSearch_Model_Resource_Query
     */
    public function loadByQueryText(Magento_Core_Model_Abstract $object, $value)
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
     * @param Magento_Core_Model_Abstract $object
     * @param int|string $value
     * @param null|string $field
     * @return Magento_CatalogSearch_Model_Resource_Query
     */
    public function load(Magento_Core_Model_Abstract $object, $value, $field = null)
    {
        if (is_numeric($value)) {
            return parent::load($object, $value);
        } else {
            $this->loadByQuery($object, $value);
        }
        return $this;
    }

    /**
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_CatalogSearch_Model_Resource_Query
     */
    public function _beforeSave(Magento_Core_Model_Abstract $object)
    {
        $object->setUpdatedAt($this->formatDate(Mage::getModel('Magento_Core_Model_Date')->gmtTimestamp()));
        return $this;
    }
}
