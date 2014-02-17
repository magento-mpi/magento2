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
     * @param \Magento\PageCache\Model\Config $config
     * @param \Magento\App\PageCache\Cache $cache
     * @param \Magento\PageCache\Helper\Data $helper
     */
    public function __construct(
        \Magento\PageCache\Model\Config $config,
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
        $object = $observer->getEvent();
        if($object instanceof \Magento\Object\IdentityInterface) {
            if($this->_config->getType() == \Magento\PageCache\Model\Config::BUILT_IN) {
                $this->_cache->clean($object->getIdentities());
            } else {
                $preparedTags = implode('|', $object->getIdentities());
                $curl = new \Magento\HTTP\Adapter\Curl();
                $curl->setOptions(array(CURLOPT_CUSTOMREQUEST => 'PURGE'));
                $curl->write('', $this->_helper->getUrl('*'), '1.1', "X-Magento-Tags-Pattern: {$preparedTags}");
            }
        }
    }
}
