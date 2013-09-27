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
     * @param Magento_Core_Model_Resource_Setup_Context $context
     * @param Magento_Core_Model_CacheInterface $cache
     * @param Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Model_Resource_Theme_CollectionFactory $themeCollFactory
     * @param Magento_Widget_Model_Widget_InstanceFactory $widgetFactory
     * @param Magento_Banner_Model_BannerFactory $bannerFactory
     * @param string $resourceName
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        Magento_Core_Model_Resource_Setup_Context $context,
        Magento_Core_Model_CacheInterface $cache,
        Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Model_Resource_Theme_CollectionFactory $themeCollFactory,
        Magento_Widget_Model_Widget_InstanceFactory $widgetFactory,
        Magento_Banner_Model_BannerFactory $bannerFactory,
        $resourceName,
        $moduleName = 'Magento_Banner',
        $connectionName = ''
    ) {
        $this->_themeCollFactory = $themeCollFactory;
        $this->_widgetFactory = $widgetFactory;
        $this->_bannerFactory = $bannerFactory;
        parent::__construct(
            $context, $cache, $migrationFactory, $coreData, $resourceName, $moduleName, $connectionName
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
