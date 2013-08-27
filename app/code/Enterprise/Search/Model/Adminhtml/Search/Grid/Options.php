<?php
/**
 * Options for Query Id column
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_Search_Model_Adminhtml_Search_Grid_Options implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var Magento_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * @var Magento_Core_Model_Registry
     */
    protected $_registryManager;

    /**
     * Enterprise_Search_Model_Resource_Recommendations
     */
    protected $_searchResourceModel;

    /**
     * @param Magento_Core_Controller_Request_Http $request
     * @param Magento_Core_Model_Registry $registry
     * @param Enterprise_Search_Model_Resource_Recommendations $searchResourceModel
     */
    public function __construct(
        Magento_Core_Controller_Request_Http $request,
        Magento_Core_Model_Registry $registry,
        Enterprise_Search_Model_Resource_Recommendations $searchResourceModel
    ) {
        $this->_request = $request;
        $this->_registryManager = $registry;
        $this->_searchResourceModel = $searchResourceModel;
    }

    /**
     * Retrieve selected related queries from grid
     *
     * @return array
     */
    public function toOptionArray()
    {
        $queries = $this->_request->getPost('selected_queries');

        $currentQueryId = $this->_registryManager->registry('current_catalog_search')->getId();
        $queryIds = array();
        if (is_null($queries) && !empty($currentQueryId)) {
            $queryIds = $this->_searchResourceModel->getRelatedQueries($currentQueryId);
        }
        return $queryIds;
    }
}
