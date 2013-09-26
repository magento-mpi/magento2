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
class Magento_Persistent_Model_Resource_Session extends Magento_Core_Model_Resource_Db_Abstract
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
     * @var Magento_Persistent_Model_SessionFactory
     */
    protected $_sessionFactory;

    /**
     * Class constructor
     *
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Persistent_Model_SessionFactory $sessionFactory
     */
    public function __construct(
        Magento_Core_Model_Resource $resource,
        Magento_Persistent_Model_SessionFactory $sessionFactory
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
     * @param Magento_Persistent_Model_Session $object
     * @return Zend_Db_Select
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
     * @return Magento_Persistent_Model_Resource_Session
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
     * @return Magento_Persistent_Model_Resource_Session
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
