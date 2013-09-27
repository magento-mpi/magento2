<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Resource Setup Model
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Magento_User_Model_Resource_Setup extends Magento_Core_Model_Resource_Setup
{
    /**
     * Role model factory
     *
     * @var Magento_User_Model_RoleFactory
     */
    protected $_roleCollectionFactory;

    /**
     * Factory for user rules model
     *
     * @var Magento_User_Model_RulesFactory
     */
    protected $_rulesCollectionFactory;

    /**
     * Role model factory
     *
     * @var Magento_User_Model_RoleFactory
     */
    protected $_roleFactory;

    /**
     * Rules model factory
     *
     * @var Magento_User_Model_RulesFactory
     */
    protected $_rulesFactory;

    public function __construct(
        Magento_Core_Model_Resource_Setup_Context $context,
        Magento_User_Model_Resource_Role_CollectionFactory $roleCollectionFactory,
        Magento_User_Model_Resource_Rules_CollectionFactory $rulesCollectionFactory,
        Magento_User_Model_RoleFactory $roleFactory,
        Magento_User_Model_RulesFactory $rulesFactory,
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
     * @return Magento_User_Model_Resource_Role_Collection
     */
    public function createRoleCollection()
    {
        return $this->_roleCollectionFactory->create();
    }

    /**
     * Creates rules collection
     *
     * @return Magento_User_Model_Resource_Rules_Collection
     */
    public function createRulesCollection()
    {
        return $this->_rulesCollectionFactory->create();
    }

    /**
     * Creates role model
     *
     * @return Magento_User_Model_Role
     */
    public function createRole()
    {
        return $this->_roleFactory->create();
    }

    /**
     * Creates rules model
     *
     * @return Magento_User_Model_Rules
     */
    public function createRules()
    {
        return $this->_rulesFactory->create();
    }
}
