<?php
/**
 * Routes configuration model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Route;

class Config implements \Magento\Core\Model\Route\ConfigInterface
{
    /**
     * @var \Magento\Core\Model\Route\Config\Reader
     */
    protected $_reader;

    /**
     * @var \Magento\Cache\FrontendInterface
     */
    protected $_cache;

    /**
     * @var string
     */
    protected $_cacheId;

    /**
     * @param \Magento\Core\Model\Route\Config\Reader $reader
     * @param \Magento\Config\CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        \Magento\Core\Model\Route\Config\Reader $reader,
        \Magento\Config\CacheInterface $cache,
        $cacheId = 'RoutesConfig'
    ) {
        $this->_reader = $reader;
        $this->_cache = $cache;
        $this->_cacheId = $cacheId;
    }

    /**
     * Fetch routes from configs by area code and router id
     *
     * @param string $areaCode
     * @param string $routerId
     * @return array
     */
    public function getRoutes($areaCode, $routerId)
    {
        $cacheId = $areaCode . '::'  . $this->_cacheId . '-' . $routerId;
        $cachedRoutes = unserialize($this->_cache->load($cacheId));
        if (is_array($cachedRoutes)) {
            return $cachedRoutes;
        }

        $routes = array();
        $areaConfig = $this->_reader->read($areaCode);
        if (array_key_exists($routerId, $areaConfig)) {
            $routes = $areaConfig[$routerId]['routes'];
            $this->_cache->save(serialize($routes), $cacheId);
        }

        return $routes;
    }
}
