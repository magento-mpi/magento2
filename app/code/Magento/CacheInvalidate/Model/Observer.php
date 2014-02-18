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
     * @var \Magento\HTTP\Adapter\Curl
     */
    protected $_curlAdapter;

    /**
     * Constructor
     *
     * @param \Magento\PageCache\Model\Config $config
     * @param \Magento\App\PageCache\Cache $cache
     * @param \Magento\PageCache\Helper\Data $helper
     * @param \Magento\HTTP\Adapter\Curl $curlAdapter
     */
    public function __construct(
        \Magento\PageCache\Model\Config $config,
        \Magento\App\PageCache\Cache $cache,
        \Magento\PageCache\Helper\Data $helper,
        \Magento\HTTP\Adapter\Curl $curlAdapter
    ){
        $this->_config = $config;
        $this->_cache = $cache;
        $this->_helper = $helper;
        $this->_curlAdapter = $curlAdapter;
    }

    /**
     * If Varnish caching is enabled it collects array of tags
     * of incoming object and asks to clean cache.
     *
     * @param \Magento\Event\Observer $observer
     */
    public function invalidateVarnish(\Magento\Event\Observer $observer)
    {
        $object = $observer->getEvent()->getObject();
        if($object instanceof \Magento\Object\IdentityInterface) {
            if($this->_config->getType() == \Magento\PageCache\Model\Config::VARNISH) {
                $this->sendPurgeRequest(implode('|', $object->getIdentities()));
            }
        }
    }

    /**
     * Flash Built-In cache
     *
     * @param \Magento\Event\Observer $observer
     */
    public function flushAllCache(\Magento\Event\Observer $observer)
    {
        if($this->_config->getType() == \Magento\PageCache\Model\Config::VARNISH) {
            $this->sendPurgeRequest('.*');
        }
    }

    /**
     * Invalidate cache by tags pattern
     *
     * @param string $tagsPattern
     */
    protected function sendPurgeRequest($tagsPattern)
    {
        $this->_curlAdapter->setOptions(array(CURLOPT_CUSTOMREQUEST => 'PURGE'));
        $this->_curlAdapter->write('', $this->_helper->getUrl('*'), '1.1', "X-Magento-Tags-Pattern: {$tagsPattern}");
        $this->_curlAdapter->close();
    }
}
