<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Persistent
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Persistent Session Resource Model
 */
namespace Magento\Persistent\Model\Resource;

class Session extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Use is object new method for object saving
     *
     * @var boolean
     */
    protected $_useIsObjectNew = true;

    /**
     * Session factory
     *
     * @var \Magento\Persistent\Model\SessionFactory
     */
    protected $_sessionFactory;

    /**
     * Class constructor
     *
     * @param \Magento\App\Resource $resource
     * @param \Magento\Persistent\Model\SessionFactory $sessionFactory
     */
    public function __construct(
        \Magento\App\Resource $resource,
        \Magento\Persistent\Model\SessionFactory $sessionFactory
    ) {
        $this->_sessionFactory = $sessionFactory;
        parent::__construct($resource);
    }

    /**
     * Initialize connection and define main table and primary key
     */
    protected function _construct()
    {
        $this->_init('persistent_session', 'persistent_id');
    }

    /**
     * Add expiration date filter to select
     *
     * @param string $field
     * @param mixed $value
     * @param \Magento\Persistent\Model\Session $object
     * @return \Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        if (!$object->getLoadExpired()) {
            $tableName = $this->getMainTable();
            $select->join(array('customer' => $this->getTable('customer_entity')),
                'customer.entity_id = ' . $tableName . '.customer_id'
            )->where($tableName . '.updated_at >= ?', $object->getExpiredBefore());
        }

        return $select;
    }

    /**
     * Delete customer persistent session by customer id
     *
     * @param int $customerId
     * @return \Magento\Persistent\Model\Resource\Session
     */
    public function deleteByCustomerId($customerId)
    {
        $this->_getWriteAdapter()->delete($this->getMainTable(), array('customer_id = ?' => $customerId));
        return $this;
    }

    /**
     * Check if such session key allowed (not exists)
     *
     * @param string $key
     * @return bool
     */
    public function isKeyAllowed($key)
    {
        $sameSession = $this->_sessionFactory->create()->setLoadExpired();
        $sameSession->loadByCookieKey($key);
        return !$sameSession->getId();
    }

    /**
     * Delete expired persistent sessions
     *
     * @param  $websiteId
     * @param  $expiredBefore
     * @return \Magento\Persistent\Model\Resource\Session
     */
    public function deleteExpired($websiteId, $expiredBefore)
    {
        $this->_getWriteAdapter()->delete(
            $this->getMainTable(),
            array(
                'website_id = ?' => $websiteId,
                'updated_at < ?' => $expiredBefore,
            )
        );
        return $this;
    }
}
