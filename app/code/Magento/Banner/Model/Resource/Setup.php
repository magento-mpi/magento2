<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Model\Resource;

/**
 * Banner Setup Resource Model
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
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
     * @param \Magento\Core\Model\Resource\Setup\Context $context
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\Core\Model\CacheInterface $cache
     * @param \Magento\Core\Model\Resource\Setup\MigrationFactory $migrationFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Model\Resource\Theme\CollectionFactory $themeCollFactory
     * @param \Magento\Widget\Model\Widget\InstanceFactory $widgetFactory
     * @param \Magento\Banner\Model\BannerFactory $bannerFactory
     * @param string $resourceName
     * @param string $moduleName
     * @param string $connectionName
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Core\Model\Resource\Setup\Context $context,
        \Magento\Core\Model\Config $config,
        \Magento\Core\Model\CacheInterface $cache,
        \Magento\Core\Model\Resource\Setup\MigrationFactory $migrationFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Model\Resource\Theme\CollectionFactory $themeCollFactory,
        \Magento\Widget\Model\Widget\InstanceFactory $widgetFactory,
        \Magento\Banner\Model\BannerFactory $bannerFactory,
        $resourceName,
        $moduleName = 'Magento_Banner',
        $connectionName = ''
    ) {
        $this->_themeCollFactory = $themeCollFactory;
        $this->_widgetFactory = $widgetFactory;
        $this->_bannerFactory = $bannerFactory;
        parent::__construct($context, $config, $cache, $migrationFactory, $coreData,
            $resourceName, $moduleName, $connectionName
        );
    }


    /**
     * @return \Magento\Banner\Model\BannerFactory
     */
    public function getBannerInstance()
    {
        return $this->_bannerFactory->create();
    }

    /**
     * @return \Magento\Core\Model\Resource\Theme\Collection
     */
    public function getThemeCollection()
    {
        return $this->_themeCollFactory->create();
    }

    /**
     * @return \Magento\Widget\Model\Widget\Instance
     */
    public function getWidgetInstance()
    {
        return $this->_widgetFactory->create();
    }
}
