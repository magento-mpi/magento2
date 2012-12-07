<?php
/**
 * Web API User resource model.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Model_Resource_Acl_User extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * @var Mage_Webapi_Helper_Data
     */
    protected $_helper;

    /**
     * Class constructor.
     *
     * @param Mage_Core_Model_Resource $resource
     * @param Mage_Webapi_Helper_Data $helper
     */
    public function __construct(Mage_Core_Model_Resource $resource, Mage_Webapi_Helper_Data $helper)
    {
        $this->_helper = $helper;
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
     * @return Mage_Webapi_Model_Resource_Acl_User
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(
            array(
                'field' => 'api_key',
                'title' => $this->_helper->__('API Key')
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
