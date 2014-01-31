<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Model\Resource;

use Magento\Core\Model\Resource\Db\AbstractDb;

/**
 * Catalog search query resource model
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Query extends AbstractDb
{
    /**
     * Date
     *
     * @var \Magento\Core\Model\Date
     */
    protected $_date;

    /**
     * @var \Magento\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @param \Magento\App\Resource $resource
     * @param \Magento\Core\Model\Date $date
     * @param \Magento\Stdlib\DateTime $dateTime
     */
    public function __construct(
        \Magento\App\Resource $resource,
        \Magento\Core\Model\Date $date,
        \Magento\Stdlib\DateTime $dateTime
    ) {
        $this->_date = $date;
        $this->dateTime = $dateTime;
        parent::__construct($resource);
    }

    /**
     * Init resource data
     *
     * @return void
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
     * @return $this
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
     * @return $this
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
     * @return $this|AbstractDb
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
     * @return $this
     */
    public function _beforeSave(\Magento\Core\Model\AbstractModel $object)
    {
        $object->setUpdatedAt($this->dateTime->formatDate($this->_date->gmtTimestamp()));
        return $this;
    }
}
