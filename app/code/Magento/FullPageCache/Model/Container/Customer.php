<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Container for cache lifetime equated with session lifetime
 */
namespace Magento\FullPageCache\Model\Container;

class Customer extends \Magento\FullPageCache\Model\Container\AbstractContainer
{
    /**
     * @var \Magento\App\ConfigInterface
     */
    protected $_coreConfig;

    /**
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\FullPageCache\Model\Cache $fpcCache
     * @param \Magento\FullPageCache\Model\Container\Placeholder $placeholder
     * @param \Magento\Registry $coreRegistry
     * @param \Magento\FullPageCache\Helper\Url $urlHelper
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\View\LayoutInterface $layout
     * @param \Magento\App\ConfigInterface $coreConfig
     */
    public function __construct(
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\FullPageCache\Model\Cache $fpcCache,
        \Magento\FullPageCache\Model\Container\Placeholder $placeholder,
        \Magento\Registry $coreRegistry,
        \Magento\FullPageCache\Helper\Url $urlHelper,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\View\LayoutInterface $layout,
        \Magento\App\ConfigInterface $coreConfig
    ) {
        parent::__construct(
            $eventManager, $fpcCache, $placeholder, $coreRegistry, $urlHelper, $coreStoreConfig, $layout
        );
        $this->_coreConfig = $coreConfig;
    }

    /**
     * Save data to cache storage and set cache lifetime equal with default cookie lifetime
     *
     * @param string $data
     * @param string $id
     * @param array $tags
     * @param null $lifetime
     * @return \Magento\FullPageCache\Model\Container\AbstractContainer
     */
    protected function _saveCache($data, $id, $tags = array(), $lifetime = null)
    {
        $lifetime = $this->_coreConfig->getValue(
            \Magento\Core\Model\Session\Config::XML_PATH_COOKIE_LIFETIME,
            'default'
        );
        return parent::_saveCache($data, $id, $tags, $lifetime);
    }
}
