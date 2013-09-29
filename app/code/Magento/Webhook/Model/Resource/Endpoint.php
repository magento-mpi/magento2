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
namespace Magento\Webhook\Model\Resource;

class Endpoint extends \Magento\Core\Model\Resource\Db\AbstractDb
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
