<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Interface of a cache frontend - an ultimate publicly available interface to an actual cache storage
 */
interface Magento_Cache_FrontendInterface
{
    /**
     * Test if a cache is available for the given id
     *
     * @param string $id Cache id
     * @return int|bool Last modified time of cache entry if it is available, false otherwise
     */
    public function test($id);

    /**
     * Load cache record by its unique identifier
     *
     * @param string $id
     * @return string|bool
     */
    public function load($id);

    /**
     * Save cache record
     *
     * @param string $data
     * @param string $id
     * @param array $tags
     * @param int|bool|null $lifeTime
     * @return bool
     */
    public function save($data, $id, array $tags = array(), $lifeTime = null);

    /**
     * Remove cache record by its unique identifier
     *
     * @param string $id
     * @return bool
     */
    public function remove($id);

    /**
     * Clean cache records MATCHING ALL specified tags
     *
     * @param string $mode
     * @param array $tags
     * @return bool
     */
    public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, array $tags = array());

    /**
     * Clean all cache records managed by a frontend
     *
     * @return bool
     */
    public function flush();

    /**
     * Retrieve backend instance
     *
     * @return Zend_Cache_Backend
     * @todo remove as soon as direct backend manipulations are eliminated
     */
    public function getBackend();

    /**
     * Retrieve frontend instance compatible with Zend_Locale_Data::setCache() to be used as a workaround
     *
     * @return Zend_Cache_Core
     */
    public function getLowLevelFrontend();
}
