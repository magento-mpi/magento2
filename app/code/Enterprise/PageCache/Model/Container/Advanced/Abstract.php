<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract advanced placeholder container
 */
abstract class Enterprise_PageCache_Model_Container_Advanced_Abstract
    extends Enterprise_PageCache_Model_Container_Abstract
{

    /**
     * Get container individual additional cache id
     *
     * @return string | false
     */
    abstract protected function _getAdditionalCacheId();

    /**
     * Load cached data by cache id
     *
     * @param string $id
     * @return string | false
     */
    protected function _loadCache($id)
    {
        $cacheRecord = parent::_loadCache($id);
        if (!$cacheRecord) {
            return false;
        }

        $cacheRecord = json_decode($cacheRecord, true);
        if (!$cacheRecord) {
            return false;
        }

        return isset($cacheRecord[$this->_getAdditionalCacheId()])
            ? $cacheRecord[$this->_getAdditionalCacheId()] : false;
    }

    /**
     * Save data to cache storage. Store many block instances in one cache record depending on additional cache ids.
     *
     * @param string $data
     * @param string $id
     * @param array $tags
     * @param null|int $lifetime
     * @return Enterprise_PageCache_Model_Container_Advanced_Abstract
     */
    protected function _saveCache($data, $id, $tags = array(), $lifetime = null)
    {
        $additionalCacheId = $this->_getAdditionalCacheId();
        if (!$additionalCacheId) {
            Mage::throwException(__('Please enter an additional ID.'));
        }

        $tags[] = Enterprise_PageCache_Model_Processor::CACHE_TAG;
        if (is_null($lifetime)) {
            $lifetime = $this->_placeholder->getAttribute('cache_lifetime') ?
                $this->_placeholder->getAttribute('cache_lifetime') : false;
        }

        /**
         * Replace all occurrences of session_id with unique marker
         */
        Enterprise_PageCache_Helper_Url::replaceSid($data);

        $result = array();

        $cacheRecord = parent::_loadCache($id);
        if ($cacheRecord) {
            $cacheRecord = json_decode($cacheRecord, true);
            if ($cacheRecord) {
                $result = $cacheRecord;
            }
        }

        $result[$additionalCacheId] = $data;

        $this->_fpcCache->save(json_encode($result), $id, $tags, $lifetime);
        return $this;
    }
}
