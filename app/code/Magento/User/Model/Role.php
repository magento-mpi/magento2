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
 * @method \Magento\User\Model\Resource\Role _getResource()
 * @method \Magento\User\Model\Resource\Role getResource()
 * @method int getParentId()
 * @method \Magento\User\Model\Role setParentId(int $value)
 * @method int getTreeLevel()
 * @method \Magento\User\Model\Role setTreeLevel(int $value)
 * @method int getSortOrder()
 * @method \Magento\User\Model\Role setSortOrder(int $value)
 * @method string getRoleType()
 * @method \Magento\User\Model\Role setRoleType(string $value)
 * @method int getUserId()
 * @method \Magento\User\Model\Role setUserId(int $value)
 * @method string getRoleName()
 * @method \Magento\User\Model\Role setRoleName(string $value)
 *
 * @category    Magento
 * @package     Magento_User
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\User\Model;

class Role extends \Magento\Core\Model\AbstractModel
{

    /**
     * @var \Magento\User\Model\Resource\Role\User\CollectionFactory
     */
    protected $_userRolesFactory;

    /**
     * @param \Magento\User\Model\Resource\Role\User\CollectionFactory $userRolesFactory
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\User\Model\Resource\Role $resource
     * @param \Magento\User\Model\Resource\Role\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\User\Model\Resource\Role\User\CollectionFactory $userRolesFactory,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\User\Model\Resource\Role $resource,
        \Magento\User\Model\Resource\Role\Collection $resourceCollection,
        array $data = array()
    ) {
        $this->_userRolesFactory = $userRolesFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @inheritdoc
     */
    public function __sleep()
    {
        $properties = parent::__sleep();
        return array_diff($properties, array('_userRolesFactory', '_resource', '_resourceCollection'));
    }

    /**
     * @inheritdoc
     */
    public function __wakeup()
    {
        parent::__wakeup();
        $objectManager = \Magento\Core\Model\ObjectManager::getInstance();
        $this->_userRolesFactory = $objectManager->get('Magento\User\Model\Resource\Role\User\CollectionFactory');
        $this->_resource = $objectManager->get('Magento\User\Model\Resource\Role');
        $this->_resourceCollection = $objectManager->get('Magento\User\Model\Resource\Role\Collection');
    }

    /**
     * @var string
     */
    protected $_eventPrefix = 'admin_roles';

    protected function _construct()
    {
        $this->_init('Magento\User\Model\Resource\Role');
    }

    /**
     * Update object into database
     *
     * @return \Magento\User\Model\Role
     */
    public function update()
    {
        $this->getResource()->update($this);
        return $this;
    }

    /**
     * Retrieve users collection
     *
     * @return \Magento\User\Model\Resource\Role\User\Collection
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
