<?php
/**
 * Options for Query Id column
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Solr\Model\Adminhtml\Search\Grid;

class Options implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registryManager;

    /**
     * @var \Magento\Solr\Model\Resource\Recommendations $_searchResourceModel
     */
    protected $_searchResourceModel;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Solr\Model\Resource\Recommendations $searchResourceModel
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Registry $registry,
        \Magento\Solr\Model\Resource\Recommendations $searchResourceModel
    ) {
        $this->_request = $request;
        $this->_registryManager = $registry;
        $this->_searchResourceModel = $searchResourceModel;
    }

    /**
     * {@inheritdoc}
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
