<?php
/**
 * {license_notice}
 *
 * @category   Varien
 * @package    Varien_Cache
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Decorator class for Zend_Cache_Backend class and its children
 */
class Varien_Cache_Backend_Decorator extends Zend_Cache_Backend implements Zend_Cache_Backend_ExtendedInterface
{
    /**
     * Prefix of compressed strings
     */
    const COMPRESSION_PREFIX = 'CACHE_COMPRESSION';

    /**
     * Concrete Cache Backend class that is being decorated
     * @var Zend_Cache_Backend
     */
    protected $_backend;

    /**
     * Array of specific options. Made in separate array to distinguish from parent options
     * @var array
     */
    protected $_decoratorOptions = array();

    /**
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->_decoratorOptions = $options;
    }

    /**
     * Set concrete Cache Backend which is being decorated
     *
     * @param Zend_Cache_Backend $backend
     */
    public function setConcreteBackend(Zend_Cache_Backend $backend)
    {
        $this->_backend = $backend;
    }

    /**
     * Get concrete Cache Backend class that is being decorated
     *
     * @return Zend_Cache_Backend
     * @throws Varien_Cache_Exception
     */
    protected function _getBackend()
    {
        if (is_null($this->_backend)) {
            throw new Varien_Cache_Exception("Decorated Cache Backend is not defined");
        }
        return $this->_backend;
    }

    /**
     * Set the frontend directives
     *
     * @param array $directives assoc of directives
     * @return void
     */
    public function setDirectives($directives)
    {
        $this->_getBackend()->setDirectives($directives);
    }

    /**
     * Test if a cache is available for the given id and (if yes) return it (false else)
     *
     * Note : return value is always "string" (unserialization is done by the core not by the backend)
     *
     * @param  string  $id                     Cache id
     * @param  boolean $doNotTestCacheValidity If set to true, the cache validity won't be tested
     * @return string|false cached datas
     */
    public function load($id, $doNotTestCacheValidity = false)
    {
        //decompression
        $data = $this->_getBackend()->load($id, $doNotTestCacheValidity);

        if ($this->_isDecompressionNeeded($data)) {
            $data = self::_decompressData($data);
        }

        return $data;
    }

    /**
     * Test if a cache is available or not (for the given id)
     *
     * @param  string $id cache id
     * @return mixed|false (a cache is not available) or "last modified" timestamp (int) of the available cache record
     */
    public function test($id)
    {
        return $this->_getBackend()->test($id);
    }

    /**
     * Save some string datas into a cache record
     *
     * Note : $data is always "string" (serialization is done by the
     * core not by the backend)
     *
     * @param  string $data             Datas to cache
     * @param  string $cacheId          Cache id
     * @param  array  $tags             Array of strings, the cache record will be tagged by each string entry
     * @param  bool   $specificLifetime If != false, set a specific lifetime for this cache record
     *                                  (null => infinite lifetime)
     * @param  int    $priority         integer between 0 (very low priority) and 10 (maximum priority) used by
     *                                  some particular backends
     * @return boolean true if no problem
     */
    public function save($data, $cacheId, $tags = array(), $specificLifetime = false, $priority = 8)
    {
        //compression
        if ((bool)$this->_decoratorOptions['compression']) {

            if ($this->_isCompressionNeeded($data)) {
                $data = self::_compressData($data);
            }
        }
        /**
         * Classes which implement Zend_Cache_Backend_ExtendedInterface have 2 different signatures in save() method.
         * The following logic covers this
         */
        if ($priority === 8) {
            return $this->_getBackend()->save($data, $cacheId, $tags, $specificLifetime);
        } else {
            return $this->_getBackend()->save($data, $cacheId, $tags, $specificLifetime, $priority);
        }
    }

    /**
     * Remove a cache record
     *
     * @param  string $id Cache id
     * @return boolean True if no problem
     */
    public function remove($id)
    {
        return $this->_getBackend()->remove($id);
    }

    /**
     * Clean some cache records
     *
     * Available modes are :
     * Zend_Cache::CLEANING_MODE_ALL (default)    => remove all cache entries ($tags is not used)
     * Zend_Cache::CLEANING_MODE_OLD              => remove too old cache entries ($tags is not used)
     * Zend_Cache::CLEANING_MODE_MATCHING_TAG     => remove cache entries matching all given tags
     *                                               ($tags can be an array of strings or a single string)
     * Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG => remove cache entries not {matching one of the given tags}
     *                                               ($tags can be an array of strings or a single string)
     * Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG => remove cache entries matching any given tags
     *                                               ($tags can be an array of strings or a single string)
     *
     * @param  string $mode Clean mode
     * @param  array  $tags Array of tags
     * @return boolean true if no problem
     */
    public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, $tags = array())
    {
        return $this->_getBackend()->clean($mode, $tags);
    }

    /**
     * Return an array of stored cache ids
     *
     * @return array array of stored cache ids (string)
     */
    public function getIds()
    {
        return $this->_getBackend()->getIds();
    }

    /**
     * Return an array of stored tags
     *
     * @return array array of stored tags (string)
     */
    public function getTags()
    {
        return $this->_getBackend()->getTags();
    }

    /**
     * Return an array of stored cache ids which match given tags
     *
     * In case of multiple tags, a logical AND is made between tags
     *
     * @param array $tags array of tags
     * @return array array of matching cache ids (string)
     */
    public function getIdsMatchingTags($tags = array())
    {
        return $this->_getBackend()->getIdsMatchingTags($tags);
    }

    /**
     * Return an array of stored cache ids which don't match given tags
     *
     * In case of multiple tags, a logical OR is made between tags
     *
     * @param array $tags array of tags
     * @return array array of not matching cache ids (string)
     */
    public function getIdsNotMatchingTags($tags = array())
    {
        return $this->_getBackend()->getIdsNotMatchingTags($tags);
    }

    /**
     * Return an array of stored cache ids which match any given tags
     *
     * In case of multiple tags, a logical AND is made between tags
     *
     * @param array $tags array of tags
     * @return array array of any matching cache ids (string)
     */
    public function getIdsMatchingAnyTags($tags = array())
    {
        return $this->_getBackend()->getIdsMatchingAnyTags($tags);
    }

    /**
     * Return the filling percentage of the backend storage
     *
     * @return int integer between 0 and 100
     */
    public function getFillingPercentage()
    {
        return $this->_getBackend()->getFillingPercentage();
    }

    /**
     * Return an array of metadatas for the given cache id
     *
     * The array must include these keys :
     * - expire : the expire timestamp
     * - tags : a string array of tags
     * - mtime : timestamp of last modification time
     *
     * @param string $id cache id
     * @return array array of metadatas (false if the cache id is not found)
     */
    public function getMetadatas($id)
    {
        return $this->_getBackend()->getMetadatas($id);
    }

    /**
     * Give (if possible) an extra lifetime to the given cache id
     *
     * @param string $id cache id
     * @param int $extraLifetime
     * @return boolean true if ok
     */
    public function touch($id, $extraLifetime)
    {
        return $this->_getBackend()->touch($id, $extraLifetime);
    }

    /**
     * Return an associative array of capabilities (booleans) of the backend
     *
     * The array must include these keys :
     * - automatic_cleaning (is automating cleaning necessary)
     * - tags (are tags supported)
     * - expired_read (is it possible to read expired cache records
     *                 (for doNotTestCacheValidity option for example))
     * - priority does the backend deal with priority when saving
     * - infinite_lifetime (is infinite lifetime can work with this backend)
     * - get_list (is it possible to get the list of cache ids and the complete list of tags)
     *
     * @return array associative of with capabilities
     */
    public function getCapabilities()
    {
        return $this->_getBackend()->getCapabilities();
    }

    /**
     * Set an option
     *
     * @param  string $name
     * @param  mixed  $value
     * @throws Zend_Cache_Exception
     * @return void
     */
    public function setOption($name, $value)
    {
        $this->_getBackend()->setOption($name, $value);
    }

    /**
     * Get the life time
     *
     * if $specificLifetime is not false, the given specific life time is used
     * else, the global lifetime is used
     *
     * @param  int $specificLifetime
     * @return int Cache life time
     */
    public function getLifetime($specificLifetime)
    {
        return $this->_getBackend()->getLifetime($specificLifetime);
    }

    /**
     * Return true if the automatic cleaning is available for the backend
     *
     * DEPRECATED : use getCapabilities() instead
     *
     * @deprecated
     * @return boolean
     */
    public function isAutomaticCleaningAvailable()
    {
        return $this->_getBackend()->isAutomaticCleaningAvailable();
    }

    /**
     * Determine system TMP directory and detect if we have read access
     *
     * inspired from Zend_File_Transfer_Adapter_Abstract
     *
     * @return string
     * @throws Zend_Cache_Exception if unable to determine directory
     */
    public function getTmpDir()
    {
        return $this->_getBackend()->getTmpDir();
    }

    /**
     * Compress data and add specific prefix
     *
     * @param string $data
     * @return string
     */
    protected static function _compressData($data)
    {
        return self::COMPRESSION_PREFIX . gzcompress($data);
    }

    /**
     * Get whether compression is needed
     *
     * @param string $data
     * @return bool
     */
    protected function _isCompressionNeeded($data)
    {
        return (strlen($data) > (int)$this->_specificOptions['compression_threshold']);
    }

    /**
     * Remove special prefix and decompress data
     *
     * @param string $data
     * @return string
     */
    protected static function _decompressData($data)
    {
        return gzuncompress(substr($data, strlen(self::COMPRESSION_PREFIX)));
    }

    /**
     * Get whether decompression is needed
     *
     * @param string $data
     * @return bool
     */
    protected function _isDecompressionNeeded($data)
    {
        return (strpos($data, self::COMPRESSION_PREFIX) === 0);
    }

}
