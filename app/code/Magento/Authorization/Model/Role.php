<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Authorization\Model;

/**
 * Admin Role Model
 *
 * @method \Magento\Authorization\Model\Resource\Role _getResource()
 * @method \Magento\Authorization\Model\Resource\Role getResource()
 * @method int getParentId()
 * @method \Magento\Authorization\Model\Role setParentId(int $value)
 * @method int getTreeLevel()
 * @method \Magento\Authorization\Model\Role setTreeLevel(int $value)
 * @method int getSortOrder()
 * @method \Magento\Authorization\Model\Role setSortOrder(int $value)
 * @method string getRoleType()
 * @method \Magento\Authorization\Model\Role setRoleType(string $value)
 * @method int getUserId()
 * @method \Magento\Authorization\Model\Role setUserId(int $value)
 * @method string getUserType()
 * @method \Magento\Authorization\Model\Role setUserType(string $value)
 * @method string getRoleName()
 * @method \Magento\Authorization\Model\Role setRoleName(string $value)
 */
class Role extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'authorization_roles';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Authorization\Model\Resource\Role $resource
     * @param \Magento\Authorization\Model\Resource\Role\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Authorization\Model\Resource\Role $resource,
        \Magento\Authorization\Model\Resource\Role\Collection $resourceCollection,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function __sleep()
    {
        $properties = parent::__sleep();
        return array_diff($properties, ['_resource', '_resourceCollection']);
    }

    /**
     * {@inheritdoc}
     */
    public function __wakeup()
    {
        parent::__wakeup();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_resource = $objectManager->get('Magento\Authorization\Model\Resource\Role');
        $this->_resourceCollection = $objectManager->get('Magento\Authorization\Model\Resource\Role\Collection');
    }

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Authorization\Model\Resource\Role');
    }

    /**
     * Update object into database
     *
     * @return $this
     */
    public function update()
    {
        $this->getResource()->update($this);
        return $this;
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
