<?php
/**
 * {license_notice}
 *
 * @category   Varien
 * @package    Varien_Cache
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Varien_Cache_Core extends Zend_Cache_Core
{
    /**
     * Any string longer than threshold to be compressed during saving
     */
    const COMPRESSION_THRESHOLD = 512;

    /**
     * Any string longer than threshold to be compressed during saving
     */
    const COMPRESSION_PREFIX = 'CACHE_COMPRESSION';

    /**
     * Available options
     *
     * ====> (bool) compression :
     * - whether data should be compressed before save
     *
     * ====> (int) compression_threshold
     * - any string, which length exceeds this threshold, will be compressed (if compression is allowed)
     * @var array
     */
    protected $_specificOptions = array(
        'compression'           => false,
        'compression_threshold' => self::COMPRESSION_THRESHOLD,
    );

    /**
     * Get is compression allowed (due to possible values in xml configuration)
     *
     * @return bool
     */
    protected function _isCompressionAllowed()
    {
        return $this->_specificOptions['compression'] === true
            || $this->_specificOptions['compression'] === 'true'
            || $this->_specificOptions['compression'] === '1'
            || $this->_specificOptions['compression'] === 1;
    }

    /**
     * Make and return a cache id
     *
     * Checks 'cache_id_prefix' and returns new id with prefix or simply the id if null
     *
     * @param  string $cacheId Cache id
     * @return string Cache id (with or without prefix)
     */
    protected function _id($cacheId)
    {
        if ($cacheId !== null) {
            $cacheId = preg_replace('/([^a-zA-Z0-9_]{1,1})/', '_', $cacheId);
            if (isset($this->_options['cache_id_prefix'])) {
                $cacheId = $this->_options['cache_id_prefix'] . $cacheId;
            }
        }
        return $cacheId;
    }

    /**
     * Prepare tags
     *
     * @param array $tags
     * @return array
     */
    protected function _tags($tags)
    {
        foreach ($tags as $key=>$tag) {
            $tags[$key] = $this->_id($tag);
        }
        return $tags;
    }

    /**
     * Save some data in a cache
     *
     * @param  mixed $data                  Data to put in cache (can be another type than string if
     *                                      automatic_serialization is on)
     * @param  null|string $cacheId         Cache id (if not set, the last cache id will be used)
     * @param  array $tags                  Cache tags
     * @param  bool|int $specificLifetime   If != false, set a specific lifetime for this cache record
     *                                      (null => infinite lifetime)
     * @param  int $priority                integer between 0 (very low priority) and 10 (maximum priority) used by
     *                                      some particular backends
     * @return boolean                      True if no problem
     */
    public function save($data, $cacheId = null, $tags = array(), $specificLifetime = false, $priority = 8)
    {
        $tags = $this->_tags($tags);

        $revertSerialization = false;

        //compression
        if ($this->_isCompressionAllowed()) {
            if ($this->_options['automatic_serialization']) {
                $data = serialize($data);
                //to prevent serialization in parent class
                $this->setOption('automatic_serialization', false);
                $revertSerialization = true;
            }

            if (is_string($data) && $this->_isCompressionNeeded($data)) {
                $data = self::_compressData($data);
            }
        }
        $save = parent::save($data, $cacheId, $tags, $specificLifetime, $priority);
        if ($revertSerialization) {
            $this->setOption('automatic_serialization', true);
        }
        return $save;
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
     * Test if a cache is available for the given id and (if yes) return it (false else)
     *
     * @param  string  $cacheId                Cache id
     * @param  boolean $doNotTestCache If set to true, the cache validity won't be tested
     * @param  boolean $doNotUnserialize       Do not serialize (even if automatic_serialization is true)
     *                                         => for internal use
     * @return mixed|false Cached data
     */
    public function load($cacheId, $doNotTestCache = false, $doNotUnserialize = false)
    {
        //decompression
        if ($this->_isCompressionAllowed()) {
            $data = parent::load($cacheId, $doNotTestCache, true);

            if ($data===false) {
                // no cache available
                return false;
            }
            if ($this->_isDecompressionNeeded($data)) {
                $data = self::_decompressData($data);
            }

            if ((!$doNotUnserialize) && $this->_options['automatic_serialization']) {
                // we need to unserialize before sending the result
                return unserialize($data);
            }
        } else {
            return parent::load($cacheId, $doNotTestCache, $doNotUnserialize);
        }
        return $data;
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

    /**
     * Clean cache entries
     *
     * Available modes are :
     * 'all' (default)  => remove all cache entries ($tags is not used)
     * 'old'            => remove too old cache entries ($tags is not used)
     * 'matchingTag'    => remove cache entries matching all given tags
     *                     ($tags can be an array of strings or a single string)
     * 'notMatchingTag' => remove cache entries not matching one of the given tags
     *                     ($tags can be an array of strings or a single string)
     * 'matchingAnyTag' => remove cache entries matching any given tags
     *                     ($tags can be an array of strings or a single string)
     *
     * @param  string       $mode
     * @param  array|string $tags
     * @throws Zend_Cache_Exception
     * @return boolean True if ok
     */
    public function clean($mode = 'all', $tags = array())
    {
        $tags = $this->_tags($tags);
        return parent::clean($mode, $tags);
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
        $tags = $this->_tags($tags);
        return parent::getIdsMatchingTags($tags);
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
        $tags = $this->_tags($tags);
        return parent::getIdsNotMatchingTags($tags);
    }
}
