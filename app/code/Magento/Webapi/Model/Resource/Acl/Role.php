<?php
/**
 * Web API ACL role resource.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Resource_Acl_Role extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * @var Magento_Webapi_Helper_Data
     */
    protected $_helper;

    /**
     * Class constructor.
     *
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Webapi_Helper_Data $helper
     */
    public function __construct(Magento_Core_Model_Resource $resource, Magento_Webapi_Helper_Data $helper)
    {
        $this->_helper = $helper;
        parent::__construct($resource);
    }

    /**
     * Resource initialization.
     */
    protected function _construct()
    {
        $this->_init('webapi_role', 'role_id');
    }

    /**
     * Initialize unique fields.
     *
     * @return Magento_Webapi_Model_Resource_Acl_Role
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(
            array(
                'field' => 'role_name',
                'title' => $this->_helper->__('Role Name')
            ),
        );
        return $this;
    }

    /**
     * Get roles list for selects.
     *
     * @return array
     */
    public function getRolesList()
    {
        $adapter = $this->getReadConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), array($this->getIdFieldName(), 'role_name'))
            ->order('role_name');
        return $adapter->fetchPairs($select);
    }

    /**
     * Get all roles IDs.
     *
     * @return array
     */
    public function getRolesIds()
    {
        $adapter = $this->getReadConnection();
        $select = $adapter->select()->from($this->getMainTable(), array($this->getIdFieldName()));
        return $adapter->fetchCol($select);
    }
}
