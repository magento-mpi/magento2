<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\FileResolution\Fallback\CacheData;

use Magento\View\Design\FileResolution\Fallback;

class Flat implements Fallback\CacheDataInterface
{
    /**
     * @var Fallback\Cache
     */
    private $cache;

    /**
     * @param Fallback\Cache $cache
     */
    public function __construct(Fallback\Cache $cache)
    {
        $this->cache = $cache;
    }
    /**
     * Retrieve cached file path
     *
     * @param string $type
     * @param string $file
     * @param array $params
     * @return string
     */
    public function getFromCache($type, $file, array $params)
    {
        $cacheId = $this->getCacheId($type, $file, $params);
        return $this->cache->load($cacheId);
    }

    /**
     * Save calculated file path
     *
     * @param string $path
     * @param string $type
     * @param string $file
     * @param array $params
     * @return bool
     */
    public function saveToCache($path, $type, $file, array $params)
    {
        $cacheId = $this->getCacheId($type, $file, $params);
        return $this->cache->save($path, $cacheId);
    }

    /**
     * Generate cache ID
     *
     * @param string $type
     * @param string $file
     * @param array $params
     * @return string
     */
    protected function getCacheId($type, $file, array $params)
    {
        return sprintf(
            "type:%s|area:%s|theme:%s|locale:%s|module:%s_%s|file:%s",
            $type,
            !empty($params['area']) ? $params['area'] : '',
            !empty($params['theme']) ? $params['theme']->getThemePath() : '',
            !empty($params['locale']) ? $params['locale'] : '',
            !empty($params['namespace']) ? $params['namespace'] : '',
            !empty($params['module']) ? $params['module'] : '',
            $file
        );
    }
}
