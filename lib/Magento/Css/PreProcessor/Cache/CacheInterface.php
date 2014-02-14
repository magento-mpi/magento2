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
     * @return null|string
     */
    public function get();

    /**
     * @param array $data
     */
    public function add($data);

    /**
     * @param \Magento\View\Publisher\FileInterface $cachedFile
     * @return $this
     */
    public function save($cachedFile);
}
