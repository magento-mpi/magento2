<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor\Cache\Plugin;

use Magento\Less\PreProcessor;
use Magento\Css\PreProcessor\Cache\CacheManager;
use Magento\Css\PreProcessor\Cache\Import\Cache;

/**
 * Plugin for less caching
 */
class InstructionPreProcessor
{
    /**
     * @var CacheManager
     */
    protected $cacheManager;

    /**
     * @param CacheManager $cacheManager
     */
    public function __construct(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }

    /**
     * Add to cache all pre-processed files that are related to initial less file
     *
     * @param PreProcessor\File\FileList $fileList
     * @return PreProcessor\File\FileList
     */
    public function afterProcessLessInstructions($fileList)
    {
        foreach ($fileList as $lessFile) {
            $this->cacheManager->addToCache(Cache::IMPORT_CACHE, $lessFile);
        }
        return $fileList;
    }
}
