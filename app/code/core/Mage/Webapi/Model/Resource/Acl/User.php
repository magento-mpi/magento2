<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Web API User resource model
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Model_Resource_Acl_User extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('webapi_user', 'user_id');
    }

    /**
     * Initialize unique fields
     *
     * @return Mage_Webapi_Model_Resource_Acl_User
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(
            array(
                'field' => 'api_key',
                'title' => $this->__('API Key')
            ),
        );
        return $this;
    }

    /**
     * Get role users
     *
     * @param int $roleId
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
