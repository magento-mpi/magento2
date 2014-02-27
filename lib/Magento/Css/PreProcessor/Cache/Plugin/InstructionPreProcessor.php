<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor\Cache\Plugin;

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
     * @param \Magento\Less\PreProcessor $subject
     * @param \Magento\Less\PreProcessor\File\Less[] $fileList
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterProcessLessInstructions(\Magento\Less\PreProcessor $subject, array $fileList)
    {
        foreach ($fileList as $lessFile) {
            $this->cacheManager->addToCache(Cache::IMPORT_CACHE, $lessFile);
        }
    }
}
