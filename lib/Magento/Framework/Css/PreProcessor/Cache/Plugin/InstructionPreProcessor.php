<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Css\PreProcessor\Cache\Plugin;

use Magento\Framework\Css\PreProcessor\Cache\CacheManager;
use Magento\Framework\Css\PreProcessor\Cache\Import\Cache;

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
     * @param \Magento\Framework\Less\PreProcessor $subject
     * @param array $fileList
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterProcessLessInstructions(\Magento\Framework\Less\PreProcessor $subject, $fileList)
    {
        foreach ($fileList as $lessFile) {
            $this->cacheManager->addToCache(Cache::IMPORT_CACHE, $lessFile);
        }

        return $fileList;
    }
}
