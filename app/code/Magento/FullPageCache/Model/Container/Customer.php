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
     * @var \Magento\Core\Model\Config
     */
    protected $_coreConfig;

    /**
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\FullPageCache\Model\Cache $fpcCache
     * @param \Magento\FullPageCache\Model\Container\Placeholder $placeholder
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\FullPageCache\Helper\Url $urlHelper
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\Layout $layout
     * @param \Magento\Core\Model\Config $coreConfig
     */
    public function __construct(
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\FullPageCache\Model\Cache $fpcCache,
        \Magento\FullPageCache\Model\Container\Placeholder $placeholder,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\FullPageCache\Helper\Url $urlHelper,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\Layout $layout,
        \Magento\Core\Model\Config $coreConfig
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
     */
    protected function _saveCache($data, $id, $tags = array(), $lifetime = null)
    {
        $lifetime = $this->_coreConfig->getValue(\Magento\Core\Model\Cookie::XML_PATH_COOKIE_LIFETIME, 'default');
        return parent::_saveCache($data, $id, $tags, $lifetime);
    }
}
