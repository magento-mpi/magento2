<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_PageCache_Model_Store_Identifier
{
    const CACHE_ID = 'current_store_id_cache';
    /**
     * @var Enterprise_PageCache_Model_Cache
     */
    protected $_fpcCache;

    /**
     * @param Enterprise_PageCache_Model_Cache $fpcCache
     */
    public function __construct(Enterprise_PageCache_Model_Cache $fpcCache)
    {
        $this->_fpcCache = $fpcCache;
    }

    /**
     * Get current store
     */
    public function getStoreId($requestId)
    {
        return (int)$this->_fpcCache->load(self::CACHE_ID . '_' . $requestId);
    }

    /**
     * Save page store
     */
    public function save($id, $requestId, array $tags)
    {
        $this->_fpcCache->save($id, self::CACHE_ID . '_' . $requestId, $tags);
    }
}
