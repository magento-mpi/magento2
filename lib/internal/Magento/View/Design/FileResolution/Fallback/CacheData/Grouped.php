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
     * {@inheritdoc}
     */
    public function getFromCache($type, $file, $area, $themePath, $locale, $module)
    {
        $sectionId = $this->getCacheSectionId($type, $area, $themePath, $locale);
        $this->loadSection($sectionId);
        $recordId = $this->getCacheRecordId($file, $module);
        if (!isset($this->cacheSections[$sectionId][$recordId])) {
            $this->cacheSections[$sectionId][$recordId] = false;
        }
        return $this->cacheSections[$sectionId][$recordId];
    }

    /**
     * {@inheritdoc}
     */
    public function saveToCache($value, $type, $file, $area, $themePath, $locale, $module)
    {
        $cacheId = $this->getCacheSectionId($type, $area, $themePath, $locale);
        $recordId = $this->getCacheRecordId($file, $module);
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
     * @param string $area
     * @param string $themePath
     * @param string $locale
     *
     * @return string
     */
    protected function getCacheSectionId($type, $area, $themePath, $locale)
    {
        return sprintf(
            "type:%s|area:%s|theme:%s|locale:%s",
            $type, $area, $themePath, $locale
        );
    }

    /**
     * Generate record ID
     *
     * @param string $file
     * @param string $module
     * @return string
     */
    protected function getCacheRecordId($file, $module)
    {
        return sprintf("module:%s|file:%s", $module, $file);
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
