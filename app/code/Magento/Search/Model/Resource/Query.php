<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model\Resource;

use Magento\Framework\Model\Resource\Db\AbstractDb;

/**
 * Search query resource model
 *
 */
class Query extends AbstractDb
{
    /**
     * Date
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     */
    public function __construct(
        \Magento\Framework\App\Resource $resource,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Stdlib\DateTime $dateTime
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
        $this->_init('search_query', 'query_id');
    }

    /**
     * Custom load model by search query string
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param string $value
     * @return $this
     */
    public function loadByQuery(\Magento\Framework\Model\AbstractModel $object, $value)
    {
        $select = $this->_getReadAdapter()->select()->from(
            $this->getMainTable()
        )->where(
            'synonym_for=? OR query_text=?',
            $value
        )->where(
            'store_id=?',
            $object->getStoreId()
        )->order(
            'synonym_for ASC'
        )->limit(
            1
        );
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
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param string $value
     * @return $this
     */
    public function loadByQueryText(\Magento\Framework\Model\AbstractModel $object, $value)
    {
        $select = $this->_getReadAdapter()->select()->from(
            $this->getMainTable()
        )->where(
            'query_text = ?',
            $value
        )->where(
            'store_id = ?',
            $object->getStoreId()
        )->limit(
            1
        );
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
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param int|string $value
     * @param null|string $field
     * @return $this|\Magento\Framework\Model\Resource\Db\AbstractDb
     */
    public function load(\Magento\Framework\Model\AbstractModel $object, $value, $field = null)
    {
        if (is_numeric($value)) {
            return parent::load($object, $value);
        } else {
            $this->loadByQuery($object, $value);
        }
        return $this;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    public function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $object->setUpdatedAt($this->dateTime->formatDate($this->_date->gmtTimestamp()));
        return $this;
    }
}
