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
     * @param string $identifier Cache id
     * @return int|bool Last modified time of cache entry if it is available, false otherwise
     */
    public function test($identifier);

    /**
     * Load cache record by its unique identifier
     *
     * @param string $identifier
     * @return string|bool
     */
    public function load($identifier);

    /**
     * Save cache record
     *
     * @param string $data
     * @param string $identifier
     * @param array $tags
     * @param int|bool|null $lifeTime
     * @return bool
     */
    public function save($data, $identifier, array $tags = array(), $lifeTime = null);

    /**
     * Remove cache record by its unique identifier
     *
     * @param string $identifier
     * @return bool
     */
    public function remove($identifier);

    /**
     * Clean cache records matching specified tags
     *
     * @param string $mode
     * @param array $tags
     * @return bool
     */
    public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, array $tags = array());

    /**
     * Retrieve backend instance
     *
     * @return Zend_Cache_Backend_Interface|Zend_Cache_Backend
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
