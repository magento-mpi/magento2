<?php
/**
 * Web API User resource model.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Resource_Acl_User extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Class constructor.
     *
     * @param Magento_Core_Model_Resource $resource
     */
    public function __construct(Magento_Core_Model_Resource $resource)
    {
        parent::__construct($resource);
    }

    /**
     * Resource initialization.
     */
    protected function _construct()
    {
        $this->_init('webapi_user', 'user_id');
    }

    /**
     * Initialize unique fields.
     *
     * @return Magento_Webapi_Model_Resource_Acl_User
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(
            array(
                'field' => 'api_key',
                'title' => __('API Key')
            ),
        );
        return $this;
    }

    /**
     * Get role users.
     *
     * @param integer $roleId
     * @return array
     */
    public function getRoleUsers($roleId)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable(), array('user_id'))
            ->where('role_id = ?', (int)$roleId);
        return $adapter->fetchCol($select);
    }
}
