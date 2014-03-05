<?php
/**
 * Options for Query Id column
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model\Adminhtml\Search\Grid;

class Options implements \Magento\Option\ArrayInterface
{
    /**
     * @var \Magento\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Registry
     */
    protected $_registryManager;

    /**
     * @var \Magento\Search\Model\Resource\Recommendations $_searchResourceModel
     */
    protected $_searchResourceModel;

    /**
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\Registry $registry
     * @param \Magento\Search\Model\Resource\Recommendations $searchResourceModel
     */
    public function __construct(
        \Magento\App\RequestInterface $request,
        \Magento\Registry $registry,
        \Magento\Search\Model\Resource\Recommendations $searchResourceModel
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
