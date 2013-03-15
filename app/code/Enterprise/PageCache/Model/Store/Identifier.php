<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_PageCache_Model_Store_Identifier
{
    /**
     * Store identifier cache prefix
     */
    const CACHE_ID = 'current_store_id_cache';

    /**
     * Cache model
     *
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
     * Get store id
     *
     * @param string $requestId
     * @return int
     */
    public function getStoreId($requestId)
    {
        return (int) $this->_fpcCache->load(self::CACHE_ID . '_' . $requestId);
    }

    /**
     * Save page store
     *
     * @param int $id
     * @param string $requestId
     * @param array $tags
     */
    public function save($id, $requestId, array $tags)
    {
        $this->_fpcCache->save($id, self::CACHE_ID . '_' . $requestId, $tags);
    }
}
