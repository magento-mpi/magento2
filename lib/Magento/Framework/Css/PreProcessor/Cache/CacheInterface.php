<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Css\PreProcessor\Cache;

/**
 * Less cache manager interface
 */
interface CacheInterface
{
    /**
     * @return $this
     */
    public function clear();

    /**
     * @return null|\Magento\Framework\View\Publisher\FileInterface
     */
    public function get();

    /**
     * @param \Magento\Framework\Less\PreProcessor\File\Less $lessFile
     * @return void
     */
    public function add($lessFile);

    /**
     * @param \Magento\Framework\View\Publisher\FileInterface $cachedFile
     * @return $this
     */
    public function save($cachedFile);
}
