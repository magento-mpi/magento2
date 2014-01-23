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
     * @var \Magento\Math\Random
     */
    protected $mathRandom;

    /**
     * @param \Magento\Core\Model\Resource\Setup\Context $context
     * @param string $resourceName
     * @param \Magento\App\CacheInterface $cache
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $attrGroupCollectionFactory
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\Widget\Model\Widget\InstanceFactory $widgetFactory
     * @param \Magento\Banner\Model\BannerFactory $bannerFactory
     * @param \Magento\Math\Random $mathRandom
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Core\Model\Resource\Setup\Context $context,
        $resourceName,
        \Magento\App\CacheInterface $cache,
        \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $attrGroupCollectionFactory,
        \Magento\Core\Model\Config $config,
        \Magento\Widget\Model\Widget\InstanceFactory $widgetFactory,
        \Magento\Banner\Model\BannerFactory $bannerFactory,
        \Magento\Math\Random $mathRandom,
        $moduleName = 'Magento_Banner',
        $connectionName = ''
    ) {
        $this->_widgetFactory = $widgetFactory;
        $this->_bannerFactory = $bannerFactory;
        $this->mathRandom = $mathRandom;
        parent::__construct($context, $resourceName, $cache, $attrGroupCollectionFactory, $config, $moduleName, $connectionName);
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
        return $this->_themeResourceFactory->create();
    }

    /**
     * @return \Magento\Widget\Model\Widget\Instance
     */
    public function getWidgetInstance()
    {
        return $this->_widgetFactory->create();
    }

    /**
     * @return string
     */
    public function getUniqueHash()
    {
        return $this->mathRandom->getUniqueHash();
    }
}
