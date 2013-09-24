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
 * @category    Magento
 * @package     Magento_Banner
 * @author      Magento Core Team <core@magentocommerce.com>
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
     * @var Magento_Core_Model_Resource_Theme_Collection
     */
    protected $_themeCollection;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Core_Model_Config_Modules_Reader $modulesReader
     * @param Magento_Core_Model_CacheInterface $cache
     * @param Magento_Core_Model_Resource_Theme_Collection $themeCollection
     * @param Magento_Widget_Model_Widget_InstanceFactory $widgetFactory
     * @param Magento_Banner_Model_BannerFactory $bannerFactory
     * @param array $moduleConfiguration
     * @param $resourceName
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Resource $resource,
        Magento_Core_Model_Config_Modules_Reader $modulesReader,
        Magento_Core_Model_CacheInterface $cache,
        Magento_Core_Model_Resource_Theme_Collection $themeCollection,
        Magento_Widget_Model_Widget_InstanceFactory $widgetFactory,
        Magento_Banner_Model_BannerFactory $bannerFactory,
        array $moduleConfiguration,
        $resourceName
    ) {
        $this->_widgetFactory = $widgetFactory;
        $this->_bannerFactory = $bannerFactory;
        $this->_themeCollection = $themeCollection;
        parent::__construct($coreData, $logger, $eventManager, $resource,
            $modulesReader, $cache, $moduleConfiguration, $resourceName
        );
    }
}
