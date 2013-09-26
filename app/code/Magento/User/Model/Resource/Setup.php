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
namespace Magento\User\Model\Resource;

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

    /**
     * Construct
     *
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Model\Config\Resource $resourcesConfig
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\Core\Model\ModuleListInterface $moduleList
     * @param \Magento\Core\Model\Resource $resource
     * @param \Magento\Core\Model\Config\Modules\Reader $modulesReader
     * @param $resourceName
     * @param \Magento\User\Model\Resource\Role\CollectionFactory $roleCollectionFactory
     * @param \Magento\User\Model\Resource\Rules\CollectionFactory $rulesCollectionFactory
     * @param \Magento\User\Model\RoleFactory $roleFactory
     * @param \Magento\User\Model\RulesFactory $rulesFactory
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Core\Model\Logger $logger,
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Model\Config\Resource $resourcesConfig,
        \Magento\Core\Model\Config $config,
        \Magento\Core\Model\ModuleListInterface $moduleList,
        \Magento\Core\Model\Resource $resource,
        \Magento\Core\Model\Config\Modules\Reader $modulesReader,
        $resourceName,
        \Magento\User\Model\Resource\Role\CollectionFactory $roleCollectionFactory,
        \Magento\User\Model\Resource\Rules\CollectionFactory $rulesCollectionFactory,
        \Magento\User\Model\RoleFactory $roleFactory,
        \Magento\User\Model\RulesFactory $rulesFactory
    ) {
        parent::__construct($logger, $eventManager, $resourcesConfig, $config, $moduleList, $resource, $modulesReader,
            $resourceName);
        $this->_roleCollectionFactory = $roleCollectionFactory;
        $this->_rulesCollectionFactory = $rulesCollectionFactory;
        $this->_roleFactory = $roleFactory;
        $this->_rulesFactory = $rulesFactory;
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
