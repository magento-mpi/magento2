<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CacheInvalidate
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CacheInvalidate\Model;

/**
 * Class Observer
 *
 * @package Magento\CacheInvalidate\Model
 */
class Observer
{
    /**
     * Application config object
     *
     * @var \Magento\App\ConfigInterface
     */
    protected $_config;

    /**
     * @var \Magento\App\PageCache\Cache
     */
    protected $_cache;

    /**
     * @var \Magento\CacheInvalidate\Helper\Data
     */
    protected $_helper;

    /**
     * Constructor
     *
     * @param \Magento\App\ConfigInterface $config
     * @param \Magento\App\PageCache\Cache $cache
     * @param \Magento\PageCache\Helper\Data $helper
     */
    public function __construct(
        \Magento\App\ConfigInterface $config,
        \Magento\App\PageCache\Cache $cache,
        \Magento\PageCache\Helper\Data $helper
    ){
        $this->_config = $config;
        $this->_cache = $cache;
        $this->_helper = $helper;
    }

    /**
     * If Built-In caching is enabled it collects array of tags
     * of incoming object and asks to clean cache.
     *
     * @param \Magento\Event\Observer $observer
     */
    public function invalidateCache(\Magento\Event\Observer $observer)
    {
        if($observer instanceof \Magento\Object\IdentityInteface) {
            if($this->_config->getType() == \Magento\PageCache\Model\Config::BUILT_IN)
            {
                $this->_cache->clean($observer->getIdentities());
            } else {
                $preparedTags = implode('|', $observer->getIdentities());
                $curl = curl_init($this->_helper->getUrl('*'));
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PURGE");
                curl_setopt($curl, CURLOPT_HTTPHEADER, "X-Magento-Tags-Pattern: {$preparedTags}");
                curl_exec($curl);
            }
        }
    }
}
