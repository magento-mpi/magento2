<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System cache proxy model
 */
namespace Magento\Core\Model\Cache;

class Proxy implements \Magento\Core\Model\CacheInterface
{
    /**
     * @var \Magento\ObjectManager
     */
    protected  $_objectManager;

    /**
     * @var \Magento\Core\Model\CacheInterface
     */
    protected  $_cache;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create cache model
     *
     * @return \Magento\Core\Model\CacheInterface|mixed
     */
    protected function _getCache()
    {
        if (null == $this->_cache) {
            $this->_cache = $this->_objectManager->get('Magento\Core\Model\Cache');
        }
        return $this->_cache;
    }

    /**
     * Get cache frontend API object
     *
     * @return \Zend_Cache_Core
     */
    public function getFrontend()
    {
        return $this->_getCache()->getFrontend();
    }

    /**
     * Load data from cache by id
     *
     * @param  string $identifier
     * @return string
     */
    public function load($identifier)
    {
        return $this->_getCache()->load($identifier);
    }

    /**
     * Save data
     *
     * @param string $data
     * @param string $identifier
     * @param array $tags
     * @param int $lifeTime
     * @return bool
     */
    public function save($data, $identifier, $tags = array(), $lifeTime = null)
    {
        return $this->_getCache()->save($data, $identifier, $tags, $lifeTime);
    }

    /**
     * Remove cached data by identifier
     *
     * @param string $identifier
     * @return bool
     */
    public function remove($identifier)
    {
        return $this->_getCache()->remove($identifier);
    }

    /**
     * Clean cached data by specific tag
     *
     * @param array $tags
     * @return bool
     */
    public function clean($tags = array())
    {
        return $this->_getCache()->clean($tags);
    }
}
