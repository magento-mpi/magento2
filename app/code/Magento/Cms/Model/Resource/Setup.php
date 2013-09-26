<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cms resource setup
 */
class Magento_Cms_Model_Resource_Setup extends Magento_Core_Model_Resource_Setup_Generic
{
    /**
     * Block factory
     *
     * @var Magento_Cms_Model_BlockFactory
     */
    protected $_blockFactory;

    /**
     * Page factory
     *
     * @var Magento_Cms_Model_PageFactory
     */
    protected $_pageFactory;

    /**
     * Construct
     *
     * @param Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory
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
     * @param $resourceName
     * @param Magento_Cms_Model_BlockFactory $blockFactory
     * @param Magento_Cms_Model_PageFactory $pageFactory
     */
    public function __construct(
        Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory,
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
        $resourceName,
        Magento_Cms_Model_BlockFactory $blockFactory,
        Magento_Cms_Model_PageFactory $pageFactory
    ) {
        parent::__construct(
            $migrationFactory, $logger, $eventManager, $resourcesConfig, $config, $moduleList, $resource, $modulesReader,
            $resourceResource, $themeResourceFactory, $themeFactory, $resourceName
        );

        $this->_blockFactory = $blockFactory;
        $this->_pageFactory = $pageFactory;
    }

    /**
     * Create block
     *
     * @return Magento_Cms_Model_Block
     */
    public function createBlock()
    {
        return $this->_blockFactory->create();
    }

    /**
     * Create page
     *
     * @return Magento_Cms_Model_Page
     */
    public function createPage()
    {
        return $this->_pageFactory->create();
    }
}
