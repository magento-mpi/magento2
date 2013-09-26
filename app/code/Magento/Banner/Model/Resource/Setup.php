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
namespace Magento\Banner\Model\Resource;

class Setup extends \Magento\Sales\Model\Resource\Setup
{
    /**
     * @var \Magento\Banner\Model\BannerFactory
     */
    protected $_bannerFactory;

    /**
     * @var \Magento\Widget\Model\Widget\InstanceFactory
     */
    protected $_widgetFactory;

    /**
     * @var \Magento\Core\Model\Resource\Theme\CollectionFactory
     */
    protected $_themeCollFactory;

    /**
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Model\Config\Resource $resourcesConfig
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\Core\Model\ModuleListInterface $moduleList
     * @param \Magento\Core\Model\Resource $resource
     * @param \Magento\Core\Model\Config\Modules\Reader $modulesReader
     * @param \Magento\Core\Model\CacheInterface $cache
     * @param \Magento\Core\Model\Resource\Setup\MigrationFactory $migrationFactory
     * @param \Magento\Core\Model\Resource\Theme\CollectionFactory $themeCollFactory
     * @param \Magento\Widget\Model\Widget\InstanceFactory $widgetFactory
     * @param \Magento\Banner\Model\BannerFactory $bannerFactory
     * @param string $resourceName
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Core\Model\Logger $logger,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Model\Config\Resource $resourcesConfig,
        \Magento\Core\Model\Config $config,
        \Magento\Core\Model\ModuleListInterface $moduleList,
        \Magento\Core\Model\Resource $resource,
        \Magento\Core\Model\Config\Modules\Reader $modulesReader,
        \Magento\Core\Model\CacheInterface $cache,
        \Magento\Core\Model\Resource\Setup\MigrationFactory $migrationFactory,
        \Magento\Core\Model\Resource\Theme\CollectionFactory $themeCollFactory,
        \Magento\Widget\Model\Widget\InstanceFactory $widgetFactory,
        \Magento\Banner\Model\BannerFactory $bannerFactory,
        $resourceName
    ) {
        $this->_themeCollFactory = $themeCollFactory;
        $this->_widgetFactory = $widgetFactory;
        $this->_bannerFactory = $bannerFactory;
        parent::__construct($logger, $coreData, $eventManager, $resourcesConfig, $config, $moduleList,
            $resource, $modulesReader, $cache, $migrationFactory, $resourceName);
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
