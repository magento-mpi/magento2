<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_PageCache_Model_RequestProcessor_Replication implements Enterprise_PageCache_Model_RequestProcessorInterface
{
    /**
     * Metadata storage model
     *
     * @var Enterprise_PageCache_Model_Metadata
     */
    protected $_metadata;

    /**
     * FPC cache model
     *
     * @var Enterprise_PageCache_Model_Cache
     */
    protected $_fpcCache;

    public function __construct(
        Enterprise_PageCache_Model_Cache_Proxy $cache,
        Enterprise_PageCache_Model_Metadata $metadata
    ) {
        $this->_fpcCache = $cache;
        $this->_metadata = $metadata;
    }

    /**
     * Check whether replication completed or not
     *
     * @return bool
     */
    protected function _isReplicationCompleted()
    {
        //Checks whether index version in cache and index version in search slave are different

        //TODO:: FPC must be invalidated if replication is completed
        //TODO:: add logic of replication state identification here after saas search module will be implemented
        return false;
    }

    /**
     * Invalidate FPC if replication is not completed
     *
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Controller_Response_Http $response
     * @param bool|string $content
     * @return bool|string
     */
    public function extractContent(
        Zend_Controller_Request_Http $request,
        Zend_Controller_Response_Http $response,
        $content
    ) {
        /**
         * $this->_metadata->getMetadata(Enterprise_PageCache_Model_Processor_Category::METADATA_CATEGORY_ID)
         * returns not empty values only we are on cached category page. For another category it will be empty value
         * So, we should run validate process for search engine only when we are on cached category page, because
         * only on this page third-party search engine uses
         */
        if ($this->_isReplicationCompleted() &&
            $this->_metadata->getMetadata(Enterprise_PageCache_Model_Processor_Category::METADATA_CATEGORY_ID)
        ) {
            $this->_fpcCache->invalidateType(Enterprise_PageCache_Model_Processor::CACHE_TAG);
        }
        return $content;
    }
}
