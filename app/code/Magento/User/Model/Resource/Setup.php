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

    /**
     * Construct
     *
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Config_Resource $resourcesConfig
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_ModuleListInterface $moduleList
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Core_Model_Config_Modules_Reader $modulesReader
     * @param Magento_Core_Model_Resource_Resource $resourceResource
     * @param Magento_Core_Model_Resource_Theme_CollectionFactory $themeResourceFactory
     * @param Magento_Core_Model_Theme_CollectionFactory $themeFactory
     * @param Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory
     * @param $resourceName
     * @param Magento_User_Model_Resource_Role_CollectionFactory $roleCollectionFactory
     * @param Magento_User_Model_Resource_Rules_CollectionFactory $rulesCollectionFactory
     * @param Magento_User_Model_RoleFactory $roleFactory
     * @param Magento_User_Model_RulesFactory $rulesFactory
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Config_Resource $resourcesConfig,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_ModuleListInterface $moduleList,
        Magento_Core_Model_Resource $resource,
        Magento_Core_Model_Config_Modules_Reader $modulesReader,
        Magento_Core_Model_Resource_Resource $resourceResource,
        Magento_Core_Model_Resource_Theme_CollectionFactory $themeResourceFactory,
        Magento_Core_Model_Theme_CollectionFactory $themeFactory,
        Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory,
        $resourceName,
        Magento_User_Model_Resource_Role_CollectionFactory $roleCollectionFactory,
        Magento_User_Model_Resource_Rules_CollectionFactory $rulesCollectionFactory,
        Magento_User_Model_RoleFactory $roleFactory,
        Magento_User_Model_RulesFactory $rulesFactory
    ) {
        parent::__construct(
            $logger, $eventManager, $resourcesConfig, $config, $moduleList, $resource, $modulesReader,
            $resourceResource, $themeResourceFactory, $themeFactory, $migrationFactory, $resourceName
        );
        $this->_roleCollectionFactory = $roleCollectionFactory;
        $this->_rulesCollectionFactory = $rulesCollectionFactory;
        $this->_roleFactory = $roleFactory;
        $this->_rulesFactory = $rulesFactory;
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
