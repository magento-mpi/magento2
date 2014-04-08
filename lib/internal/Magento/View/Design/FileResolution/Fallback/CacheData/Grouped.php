<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\FileResolution\Fallback\CacheData;

use Magento\View\Design\FileResolution\Fallback;

class Grouped implements Fallback\CacheDataInterface
{
    /**
     * @var Fallback\Cache
     */
    private $cache;

    /**
     * @var bool
     */
    private $isUpdated = false;

    /**
     * @var array
     */
    private $cacheSections = [];

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
        $sectionId = $this->getCacheSectionId($type, $params);
        $this->loadSection($sectionId);
        $recordId = $this->getCacheRecordId($file, $params);
        if (!isset($this->cacheSections[$sectionId][$recordId])) {
            $this->cacheSections[$sectionId][$recordId] = false;
        }
        return $this->cacheSections[$sectionId][$recordId];
    }

    /**
     * Save calculated file path
     *
     * @param string $value
     * @param string $type
     * @param string $file
     * @param array $params
     * @return bool
     */
    public function saveToCache($value, $type, $file, array $params)
    {
        $cacheId = $this->getCacheSectionId($type, $params);
        $recordId = $this->getCacheRecordId($file, $params);
        $this->isUpdated = true;
        return $this->cacheSections[$cacheId][$recordId] = $value;
    }

    /**
     * @param string $sectionId
     */
    private function loadSection($sectionId)
    {
        if (!isset($this->cacheSections[$sectionId])) {
            $value = $this->cache->load($sectionId);
            if ($value) {
                $this->cacheSections[$sectionId] = json_decode($value, true);
            } else {
                $this->cacheSections[$sectionId] = [];
            }
        }
    }

    /**
     * Generate section ID
     *
     * @param string $type
     * @param array $params
     * @return string
     */
    protected function getCacheSectionId($type, array $params)
    {
        return sprintf(
            "type:%s|area:%s|theme:%s|locale:%s",
            $type,
            !empty($params['area']) ? $params['area'] : '',
            !empty($params['theme']) ? $params['theme']->getThemePath() : '',
            !empty($params['locale']) ? $params['locale'] : ''
        );
    }

    /**
     * Generate record ID
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    protected function getCacheRecordId($file, array $params)
    {
        return sprintf(
            "module:%s_%s|file:%s",
            !empty($params['namespace']) ? $params['namespace'] : '',
            !empty($params['module']) ? $params['module'] : '',
            $file
        );
    }

    /**
     * Save cache
     */
    public function __destruct()
    {
        if ($this->isUpdated) {
            foreach ($this->cacheSections as $cacheId => $section) {
                $value = json_encode($section);
                $this->cache->save($value, $cacheId);
            }
        }
    }
}
