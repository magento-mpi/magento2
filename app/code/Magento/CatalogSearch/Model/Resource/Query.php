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
namespace Magento\CatalogSearch\Model\Resource;

class Query extends \Magento\Core\Model\Resource\Db\AbstractDb
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
     * @param \Magento\Core\Model\AbstractModel $object
     * @param string $value
     * @return \Magento\CatalogSearch\Model\Resource\Query
     */
    public function loadByQuery(\Magento\Core\Model\AbstractModel $object, $value)
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
     * @param \Magento\Core\Model\AbstractModel $object
     * @param string $value
     * @return \Magento\CatalogSearch\Model\Resource\Query
     */
    public function loadByQueryText(\Magento\Core\Model\AbstractModel $object, $value)
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
     * @param \Magento\Core\Model\AbstractModel $object
     * @param int|string $value
     * @param null|string $field
     * @return \Magento\CatalogSearch\Model\Resource\Query
     */
    public function load(\Magento\Core\Model\AbstractModel $object, $value, $field = null)
    {
        if (is_numeric($value)) {
            return parent::load($object, $value);
        } else {
            $this->loadByQuery($object, $value);
        }
        return $this;
    }

    /**
     * @param \Magento\Core\Model\AbstractModel $object
     * @return \Magento\CatalogSearch\Model\Resource\Query
     */
    public function _beforeSave(\Magento\Core\Model\AbstractModel $object)
    {
        $object->setUpdatedAt($this->formatDate(\Mage::getModel('Magento\Core\Model\Date')->gmtTimestamp()));
        return $this;
    }
}
