<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Resource_Setup_Context
{
    /**
     * @var Magento_Core_Model_Logger
     */
    protected $_logger;

    /**
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager;

    /**
     * @var Magento_Core_Model_Resource
     */
    protected $_resourceModel;

    /**
     * @var Magento_Core_Model_Config_Modules_Reader
     */
    protected $_modulesReader;

    /**
     * @var Magento_Core_Model_ModuleListInterface
     */
    protected $_moduleList;

    /**
     * @var Magento_Core_Model_Resource_Resource
     */
    protected $_resourceResource;

    /**
     * @var Magento_Core_Model_Resource_Setup_MigrationFactory
     */
    protected $_migrationFactory;

    /**
     * @var Magento_Core_Model_Resource_Theme_CollectionFactory
     */
    protected $_themeResourceFactory;

    /**
     * @var Magento_Core_Model_Theme_CollectionFactory
     */
    protected $_themeFactory;

    /**
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Core_Model_Config_Modules_Reader $modulesReader
     * @param Magento_Core_Model_ModuleListInterface $moduleList
     * @param Magento_Core_Model_Resource_Resource $resourceResource
     * @param Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory
     * @param Magento_Core_Model_Resource_Theme_CollectionFactory $themeResourceFactory
     * @param Magento_Core_Model_Theme_CollectionFactory $themeFactory
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Resource $resource,
        Magento_Core_Model_Config_Modules_Reader $modulesReader,
        Magento_Core_Model_ModuleListInterface $moduleList,
        Magento_Core_Model_Resource_Resource $resourceResource,
        Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory,
        Magento_Core_Model_Resource_Theme_CollectionFactory $themeResourceFactory,
        Magento_Core_Model_Theme_CollectionFactory $themeFactory
    ) {
        $this->_logger = $logger;
        $this->_eventManager = $eventManager;
        $this->_resourceModel = $resource;
        $this->_modulesReader = $modulesReader;
        $this->_moduleList = $moduleList;
        $this->_resourceResource = $resourceResource;
        $this->_migrationFactory = $migrationFactory;
        $this->_themeResourceFactory = $themeResourceFactory;
        $this->_themeFactory = $themeFactory;
    }

    /**
     * @return \Magento_Core_Model_Event_Manager
     */
    public function getEventManager()
    {
        return $this->_eventManager;
    }

    /**
     * @return \Magento_Core_Model_Logger
     */
    public function getLogger()
    {
        return $this->_logger;
    }

    /**
     * @return \Magento_Core_Model_ModuleListInterface
     */
    public function getModuleList()
    {
        return $this->_moduleList;
    }

    /**
     * @return \Magento_Core_Model_Config_Modules_Reader
     */
    public function getModulesReader()
    {
        return $this->_modulesReader;
    }

    /**
     * @return \Magento_Core_Model_Resource
     */
    public function getResourceModel()
    {
        return $this->_resourceModel;
    }

    /**
     * @return \Magento_Core_Model_Resource_Setup_MigrationFactory
     */
    public function getMigrationFactory()
    {
        return $this->_migrationFactory;
    }

    /**
     * @return \Magento_Core_Model_Resource_Resource
     */
    public function getResourceResource()
    {
        return $this->_resourceResource;
    }

    /**
     * @return \Magento_Core_Model_Theme_CollectionFactory
     */
    public function getThemeFactory()
    {
        return $this->_themeFactory;
    }

    /**
     * @return \Magento_Core_Model_Resource_Theme_CollectionFactory
     */
    public function getThemeResourceFactory()
    {
        return $this->_themeResourceFactory;
    }
}
