<?php
/**
 * Endpoint resource
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Resource_Endpoint extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize resource model
     */
    public function _construct()
    {
        $this->_init('outbound_endpoint', 'endpoint_id');
    }

    /**
     * Get endpoints associated with a given api user id.
     *
     * @param int|int[] $apiUserIds
     * @return array
     */
    public function getApiUserEndpoints($apiUserIds)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable(), array('endpoint_id'))
            ->where('api_user_id IN (?)', $apiUserIds);
        return $adapter->fetchCol($select);
    }

    /**
     * Get endpoints that do not have an associated api user
     *
     * @return array
     */
    public function getEndpointsWithoutApiUser()
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable(), array('endpoint_id'))
            ->where('api_user_id IS NULL');
        return $adapter->fetchCol($select);
    }
}
