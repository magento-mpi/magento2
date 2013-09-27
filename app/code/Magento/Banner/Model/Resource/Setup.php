<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Banner Setup Resource Model
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Magento_Banner_Model_Resource_Setup extends Magento_Sales_Model_Resource_Setup
{
    /**
     * @var Magento_Banner_Model_BannerFactory
     */
    protected $_bannerFactory;

    /**
     * @var Magento_Widget_Model_Widget_InstanceFactory
     */
    protected $_widgetFactory;

    /**
     * @var Magento_Core_Model_Resource_Theme_CollectionFactory
     */
    protected $_themeCollFactory;

    /**
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
     * @param Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory
     * @param $resourceName
     * @param Magento_Core_Model_CacheInterface $cache
     * @param Magento_Eav_Model_Resource_Entity_Attribute_Group_CollectionFactory $attrGrCollFactory
     * @param Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Model_Resource_Theme_CollectionFactory $themeCollFactory
     * @param Magento_Widget_Model_Widget_InstanceFactory $widgetFactory
     * @param Magento_Banner_Model_BannerFactory $bannerFactory
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
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
        Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory,
        $resourceName,
        Magento_Core_Model_CacheInterface $cache,
        Magento_Eav_Model_Resource_Entity_Attribute_Group_CollectionFactory $attrGrCollFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Model_Resource_Theme_CollectionFactory $themeCollFactory,
        Magento_Widget_Model_Widget_InstanceFactory $widgetFactory,
        Magento_Banner_Model_BannerFactory $bannerFactory
    ) {
        $this->_themeCollFactory = $themeCollFactory;
        $this->_widgetFactory = $widgetFactory;
        $this->_bannerFactory = $bannerFactory;
        parent::__construct(
            $logger,
            $eventManager,
            $resourcesConfig,
            $config,
            $moduleList,
            $resource,
            $modulesReader,
            $resourceResource,
            $themeResourceFactory,
            $themeFactory,
            $migrationFactory,
            $resourceName,
            $cache,
            $attrGrCollFactory,
            $migrationFactory,
            $coreData
        );
    }

    /**
     * @return \Magento_Banner_Model_BannerFactory
     */
    public function getBannerInstance()
    {
        return $this->_bannerFactory->create();
    }

    /**
     * @return \Magento_Core_Model_Resource_Theme_Collection
     */
    public function getThemeCollection()
    {
        return $this->_themeCollFactory->create();
    }

    /**
     * @return \Magento_Widget_Model_Widget_Instance
     */
    public function getWidgetInstance()
    {
        return $this->_widgetFactory->create();
    }
}
