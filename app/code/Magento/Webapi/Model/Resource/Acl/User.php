<?php
/**
 * Web API User resource model.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Resource\Acl;

class User extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Class constructor.
     *
     * @param \Magento\Core\Model\Resource $resource
     */
    public function __construct(\Magento\Core\Model\Resource $resource)
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
     * @return \Magento\Webapi\Model\Resource\Acl\User
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
