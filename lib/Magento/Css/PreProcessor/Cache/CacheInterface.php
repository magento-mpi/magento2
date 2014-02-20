<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor\Cache;

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
     * @return null|\Magento\View\Publisher\FileInterface
     */
    public function get();

    /**
     * @param \Magento\Less\PreProcessor\File\Less $lessFile
     */
    public function add($lessFile);

    /**
     * @param \Magento\View\Publisher\FileInterface $cachedFile
     * @return $this
     */
    public function save($cachedFile);
}
