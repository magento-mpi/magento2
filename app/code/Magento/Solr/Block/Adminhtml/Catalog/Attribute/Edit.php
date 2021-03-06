<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Solr\Block\Adminhtml\Catalog\Attribute;

/**
 * Enterprise attribute edit block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Edit extends \Magento\Backend\Block\Template
{
    /**
     * Search data
     *
     * @var \Magento\Solr\Helper\Data
     */
    protected $_searchData = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Solr\Helper\Data $searchData
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Solr\Helper\Data $searchData,
        array $data = []
    ) {
        $this->_searchData = $searchData;
        parent::__construct($context, $data);
    }

    /**
     * Return true if third part search engine used
     *
     * @return bool
     */
    public function isThirdPartSearchEngine()
    {
        return $this->_searchData->isThirdPartSearchEngine();
    }
}
