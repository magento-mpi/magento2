<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Product search result block
 *
 * @category   Magento
 * @package    Magento_CatalogSearch
 * @module     Catalog
 */
namespace Magento\CatalogSearch\Block;

class Result extends \Magento\View\Element\Template
{
    /**
     * Catalog Product collection
     *
     * @var \Magento\CatalogSearch\Model\Resource\Fulltext\Collection
     */
    protected $_productCollection;

    /**
     * Catalog search data
     *
     * @var \Magento\CatalogSearch\Helper\Data
     */
    protected $_catalogSearchData = null;

    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_catalogLayer;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\Layer $catalogLayer
     * @param \Magento\CatalogSearch\Helper\Data $catalogSearchData
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Layer $catalogLayer,
        \Magento\CatalogSearch\Helper\Data $catalogSearchData,
        array $data = array()
    ) {
        $this->_catalogLayer = $catalogLayer;
        $this->_catalogSearchData = $catalogSearchData;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve query model object
     *
     * @return \Magento\CatalogSearch\Model\Query
     */
    protected function _getQuery()
    {
        return $this->_catalogSearchData->getQuery();
    }

    /**
     * Prepare layout
     *
     * @return \Magento\CatalogSearch\Block\Result
     */
    protected function _prepareLayout()
    {
        // add Home breadcrumb
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {
            $title = __("Search results for: '%1'", $this->_catalogSearchData->getQueryText());

            $breadcrumbs->addCrumb('home', array(
                'label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link'  => $this->_storeManager->getStore()->getBaseUrl(),
            ))->addCrumb('search', array(
                'label' => $title,
                'title' => $title
            ));
        }

        // modify page title
        $title = __("Search results for: '%1'", $this->_catalogSearchData->getEscapedQueryText());
        $this->getLayout()->getBlock('head')->setTitle($title);

        return parent::_prepareLayout();
    }

    /**
     * Retrieve additional blocks html
     *
     * @return string
     */
    public function getAdditionalHtml()
    {
        return $this->getLayout()->getBlock('search_result_list')->getChildHtml('additional');
    }

    /**
     * Retrieve search list toolbar block
     *
     * @return \Magento\Catalog\Block\Product\ListProduct
     */
    public function getListBlock()
    {
        return $this->getChildBlock('search_result_list');
    }

    /**
     * Set search available list orders
     *
     * @return \Magento\CatalogSearch\Block\Result
     */
    public function setListOrders()
    {
        $category = $this->_catalogLayer->getCurrentCategory();
        /* @var $category \Magento\Catalog\Model\Category */
        $availableOrders = $category->getAvailableSortByOptions();
        unset($availableOrders['position']);
        $availableOrders = array_merge(array(
            'relevance' => __('Relevance')
        ), $availableOrders);

        $this->getListBlock()
            ->setAvailableOrders($availableOrders)
            ->setDefaultDirection('desc')
            ->setSortBy('relevance');

        return $this;
    }

    /**
     * Set available view mode
     *
     * @return \Magento\CatalogSearch\Block\Result
     */
    public function setListModes()
    {
        $this->getListBlock()
            ->setModes(array(
                'grid' => __('Grid'),
                'list' => __('List'))
            );
        return $this;
    }

    /**
     * Set Search Result collection
     *
     * @return \Magento\CatalogSearch\Block\Result
     */
    public function setListCollection()
    {
//        $this->getListBlock()
//           ->setCollection($this->_getProductCollection());
       return $this;
    }

    /**
     * Retrieve Search result list HTML output
     *
     * @return string
     */
    public function getProductListHtml()
    {
        return $this->getChildHtml('search_result_list');
    }

    /**
     * Retrieve loaded category collection
     *
     * @return \Magento\CatalogSearch\Model\Resource\Fulltext\Collection
     */
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $this->_productCollection = $this->getListBlock()->getLoadedProductCollection();
        }

        return $this->_productCollection;
    }

    /**
     * Retrieve search result count
     *
     * @return string
     */
    public function getResultCount()
    {
        if (!$this->getData('result_count')) {
            $size = $this->_getProductCollection()->getSize();
            $this->_getQuery()->setNumResults($size);
            $this->setResultCount($size);
        }
        return $this->getData('result_count');
    }

    /**
     * Retrieve No Result or Minimum query length Text
     *
     * @return string
     */
    public function getNoResultText()
    {
        if ($this->_catalogSearchData->isMinQueryLength()) {
            return __('Minimum Search query length is %1', $this->_getQuery()->getMinQueryLength());
        }
        return $this->_getData('no_result_text');
    }

    /**
     * Retrieve Note messages
     *
     * @return array
     */
    public function getNoteMessages()
    {
        return $this->_catalogSearchData->getNoteMessages();
    }
}
