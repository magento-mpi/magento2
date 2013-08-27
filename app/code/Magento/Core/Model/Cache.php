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
 * System cache model
 * support id and tags preffix support,
 */

class Magento_Core_Model_Cache implements Magento_Core_Model_CacheInterface
{
    /**
     * @var string
     */
    protected $_frontendIdentifier = Magento_Core_Model_Cache_Frontend_Pool::DEFAULT_FRONTEND_ID;

    /**
     * @var Magento_Core_Model_Cache_Frontend_Pool
     */
    protected $_frontendPool;

    /**
     * Cache frontend API
     *
     * @var Magento_Cache_FrontendInterface
     */
    protected $_frontend;

    /**
     * @param Magento_Core_Model_Cache_Frontend_Pool $frontendPool
     */
    public function __construct(Magento_Core_Model_Cache_Frontend_Pool $frontendPool)
    {
        $this->_frontendPool = $frontendPool;
        $this->_frontend = $frontendPool->get($this->_frontendIdentifier);
    }

    /**
     * Get cache frontend API object
     *
     * @return Magento_Cache_FrontendInterface
     */
    public function getFrontend()
    {
        return $this->_frontend;
    }

    /**
     * Load data from cache by id
     *
     * @param  string $identifier
     * @return string
     */
    public function load($identifier)
    {
        return $this->_frontend->load($identifier);
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
        return $this->_frontend->save((string)$data, $identifier, $tags, $lifeTime);
    }

    /**
     * Remove cached data by identifier
     *
     * @param string $identifier
     * @return bool
     */
    public function remove($identifier)
    {
        return $this->_frontend->remove($identifier);
    }

    /**
     * Clean cached data by specific tag
     *
     * @param array $tags
     * @return bool
     */
    public function clean($tags = array())
    {
        if ($tags) {
            $result = $this->_frontend->clean(Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG, (array)$tags);
        } else {
            /** @deprecated special case of cleaning by empty tags is deprecated after 2.0.0.0-dev42 */
            $result = false;
            /** @var $cacheFrontend Magento_Cache_FrontendInterface */
            foreach ($this->_frontendPool as $cacheFrontend) {
                if ($cacheFrontend->clean()) {
                    $result = true;
                }
            }
        }
        return $result;
    }
}
