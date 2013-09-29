<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Model\Resource;

/**
 * Resource Setup Model
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Setup extends \Magento\Core\Model\Resource\Setup
{
    /**
     * Role model factory
     *
     * @var \Magento\User\Model\RoleFactory
     */
    protected $_roleCollectionFactory;

    /**
     * Factory for user rules model
     *
     * @var \Magento\User\Model\RulesFactory
     */
    protected $_rulesCollectionFactory;

    /**
     * Role model factory
     *
     * @var \Magento\User\Model\RoleFactory
     */
    protected $_roleFactory;

    /**
     * Rules model factory
     *
     * @var \Magento\User\Model\RulesFactory
     */
    protected $_rulesFactory;

    public function __construct(
        \Magento\Core\Model\Resource\Setup\Context $context,
        \Magento\User\Model\Resource\Role\CollectionFactory $roleCollectionFactory,
        \Magento\User\Model\Resource\Rules\CollectionFactory $rulesCollectionFactory,
        \Magento\User\Model\RoleFactory $roleFactory,
        \Magento\User\Model\RulesFactory $rulesFactory,
        $resourceName,
        $moduleName = 'Magento_User',
        $connectionName = ''
    ) {
        $this->_roleCollectionFactory = $roleCollectionFactory;
        $this->_rulesCollectionFactory = $rulesCollectionFactory;
        $this->_roleFactory = $roleFactory;
        $this->_rulesFactory = $rulesFactory;
        parent::__construct($context, $resourceName, $moduleName, $connectionName);
    }


    /**
     * Creates role collection
     *
     * @return \Magento\User\Model\Resource\Role\Collection
     */
    public function createRoleCollection()
    {
        return $this->_roleCollectionFactory->create();
    }

    /**
     * Creates rules collection
     *
     * @return \Magento\User\Model\Resource\Rules\Collection
     */
    public function createRulesCollection()
    {
        return $this->_rulesCollectionFactory->create();
    }

    /**
     * Creates role model
     *
     * @return \Magento\User\Model\Role
     */
    public function createRole()
    {
        return $this->_roleFactory->create();
    }

    /**
     * Creates rules model
     *
     * @return \Magento\User\Model\Rules
     */
    public function createRules()
    {
        return $this->_rulesFactory->create();
    }
}
