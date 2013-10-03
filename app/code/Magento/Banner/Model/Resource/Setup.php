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
     * @param \Magento\Core\Model\Resource\Setup\Context $context
     * @param \Magento\Core\Model\CacheInterface $cache
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $attrGrCollFactory
     * @param \Magento\Core\Helper\Data $coreHelper
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\Widget\Model\Widget\InstanceFactory $widgetFactory
     * @param \Magento\Banner\Model\BannerFactory $bannerFactory
     * @param string $resourceName
     * @param string $moduleName
     * @param string $connectionName
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Core\Model\Resource\Setup\Context $context,
        \Magento\Core\Model\CacheInterface $cache,
        \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $attrGrCollFactory,
        \Magento\Core\Helper\Data $coreHelper,
        \Magento\Core\Model\Config $config,
        \Magento\Widget\Model\Widget\InstanceFactory $widgetFactory,
        \Magento\Banner\Model\BannerFactory $bannerFactory,
        $resourceName,
        $moduleName = 'Magento_Banner',
        $connectionName = ''
    ) {
        $this->_widgetFactory = $widgetFactory;
        $this->_bannerFactory = $bannerFactory;
        parent::__construct(
            $context,
            $cache,
            $attrGrCollFactory,
            $coreHelper,
            $config,
            $resourceName,
            $moduleName,
            $connectionName
        );
    }

    /**
     * @return \\Magento\Banner\Model\BannerFactory
     */
    public function getBannerInstance()
    {
        return $this->_bannerFactory->create();
    }

    /**
     * @return \\Magento\Core\Model\Resource\Theme\Collection
     */
    public function getThemeCollection()
    {
        return $this->_themeResourceFactory->create();
    }

    /**
     * @return \\Magento\Widget\Model\Widget\Instance
     */
    public function getWidgetInstance()
    {
        return $this->_widgetFactory->create();
    }
}
