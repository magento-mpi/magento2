<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin Role Model
 *
 * @method Magento_User_Model_Resource_Role _getResource()
 * @method Magento_User_Model_Resource_Role getResource()
 * @method int getParentId()
 * @method Magento_User_Model_Role setParentId(int $value)
 * @method int getTreeLevel()
 * @method Magento_User_Model_Role setTreeLevel(int $value)
 * @method int getSortOrder()
 * @method Magento_User_Model_Role setSortOrder(int $value)
 * @method string getRoleType()
 * @method Magento_User_Model_Role setRoleType(string $value)
 * @method int getUserId()
 * @method Magento_User_Model_Role setUserId(int $value)
 * @method string getRoleName()
 * @method Magento_User_Model_Role setRoleName(string $value)
 *
 * @category    Magento
 * @package     Magento_User
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_User_Model_Role extends Magento_Core_Model_Abstract
{

    /**
     * @var Magento_User_Model_Resource_Role_User_CollectionFactory
     */
    protected $_userRolesFactory;

    /**
     * @param Magento_User_Model_Resource_Role_User_CollectionFactory $userRolesFactory
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_User_Model_Resource_Role $resource
     * @param Magento_User_Model_Resource_Role_Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_User_Model_Resource_Role_User_CollectionFactory $userRolesFactory,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_User_Model_Resource_Role $resource,
        Magento_User_Model_Resource_Role_Collection $resourceCollection,
        array $data = array()
    ) {
        $this->_userRolesFactory = $userRolesFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @var string
     */
    protected $_eventPrefix = 'admin_roles';

    protected function _construct()
    {
        $this->_init('Magento_User_Model_Resource_Role');
    }

    /**
     * Update object into database
     *
     * @return Magento_User_Model_Role
     */
    public function update()
    {
        $this->getResource()->update($this);
        return $this;
    }

    /**
     * Retrieve users collection
     *
     * @return Magento_User_Model_Resource_Role_User_Collection
     */
    public function getUsersCollection()
    {
        return $this->_userRolesFactory->create();
    }

    /**
     * Return users for role
     *
     * @return array
     */
    public function getRoleUsers()
    {
        return $this->getResource()->getRoleUsers($this);
    }
}
