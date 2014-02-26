<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\User\Model;

/**
 * Admin Rules Model
 *
 * @method \Magento\User\Model\Resource\Rules _getResource()
 * @method \Magento\User\Model\Resource\Rules getResource()
 * @method int getRoleId()
 * @method \Magento\User\Model\Rules setRoleId(int $value)
 * @method string getResourceId()
 * @method \Magento\User\Model\Rules setResourceId(string $value)
 * @method string getPrivileges()
 * @method \Magento\User\Model\Rules setPrivileges(string $value)
 * @method int getAssertId()
 * @method \Magento\User\Model\Rules setAssertId(int $value)
 * @method string getPermission()
 * @method \Magento\User\Model\Rules setPermission(string $value)
 */
class Rules extends \Magento\Core\Model\AbstractModel
{
    /**
     * Class constructor
     *
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param Resource\Rules $resource
     * @param Resource\Permissions\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\User\Model\Resource\Rules $resource,
        \Magento\User\Model\Resource\Permissions\Collection $resourceCollection,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\User\Model\Resource\Rules');
    }

    /**
     * @return $this
     */
    public function update()
    {
        $this->getResource()->update($this);
        return $this;
    }

    /**
     * @return $this
     */
    public function saveRel()
    {
        $this->getResource()->saveRel($this);
        return $this;
    }
}
