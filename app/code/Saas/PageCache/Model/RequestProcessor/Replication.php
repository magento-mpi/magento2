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
     * @var Mage_Core_Model_Cache_TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @param Mage_Core_Model_Cache_TypeListInterface $cacheTypeList
     * @param Enterprise_PageCache_Model_Metadata $metadata
     * @param Saas_Search_Helper_Cache $cacheHelper
     */
    public function __construct(
        Mage_Core_Model_Cache_TypeListInterface $cacheTypeList,
        Enterprise_PageCache_Model_Metadata $metadata,
        Saas_Search_Helper_Cache $cacheHelper
    ) {
        $this->_cacheTypeList = $cacheTypeList;
        $this->_metadata = $metadata;
        $this->_cacheHelper = $cacheHelper;
    }

    /**
     * Check whether replication completed or not
     *
     * @return bool
     */
    protected function _isReplicationCompleted()
    {
        return $this->_cacheHelper->isReplicationCompleted();
    }

    /**
     * Invalidate FPC if replication is not completed
     *
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Controller_Response_Http $response
     * @param bool|string $content
     * @return bool|string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function extractContent(
        Zend_Controller_Request_Http $request,
        Zend_Controller_Response_Http $response,
        $content
    ) {
        if ($this->_metadata->getMetadata(Enterprise_PageCache_Model_Processor_Category::METADATA_CATEGORY_ID) &&
            $this->_isReplicationCompleted()
        ) {
            $this->_cacheTypeList->invalidate(Enterprise_PageCache_Model_Cache_Type::TYPE_IDENTIFIER);
        }
        return $content;
    }
}
