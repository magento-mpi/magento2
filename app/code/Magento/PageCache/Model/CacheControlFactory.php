<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Factory class for cache control interface
 */
namespace Magento\PageCache\Model;

class CacheControlFactory
{
    /**
     * Path to external cache controls
     */
    const XML_PATH_EXTERNAL_CACHE_CONTROLS = 'global/external_cache/controls';

    /**
     * Paths to external cache config option
     */
    const XML_PATH_EXTERNAL_CACHE_CONTROL  = 'system/external_page_cache/control';

    /**
     * Object manager
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Config
     *
     * @var \Magento\Centinel\Model\Config
     */
    protected $_config;

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Core\Model\ConfigInterface $config
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        \Magento\Core\Model\ConfigInterface $config,
        \Magento\Core\Model\Store\Config $coreStoreConfig
    ) {
        $this->_objectManager = $objectManager;
        $this->_config = $config;
        $this->_coreStoreConfig = $coreStoreConfig;
    }

    /**
     * Return all available external cache controls
     *
     * @return array
     */
    public function getCacheControls()
    {
        $controls = $this->_config->getNode(self::XML_PATH_EXTERNAL_CACHE_CONTROLS);
        return $controls->asCanonicalArray();
    }

    /**
     * Initialize proper external cache control model
     *
     * @throws \Magento\Core\Exception
     * @return \Magento\PageCache\Model\Control\ControlInterface
     */
    public function getCacheControlInstance()
    {
        $usedControl = $this->_coreStoreConfig->getConfig(self::XML_PATH_EXTERNAL_CACHE_CONTROL);
        if ($usedControl) {
            foreach ($this->getCacheControls() as $control => $info) {
                if ($control == $usedControl && !empty($info['class'])) {
                    return $this->_objectManager->get($info['class']);
                }
            }
        }
        throw new \Magento\Core\Exception(__('Failed to load external cache control'));
    }
}
