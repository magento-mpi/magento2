<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Block\Advanced;

/**
 * Advanced search result
 */
class Result extends \Magento\View\Element\Template
{
    /**
     * Url factory
     *
     * @var \Magento\UrlFactory
     */
    protected $_urlFactory;

    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_catalogLayer;

    /**
     * Catalog search advanced
     *
     * @var \Magento\CatalogSearch\Model\Advanced
     */
    protected $_catalogSearchAdvanced;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\CatalogSearch\Model\Advanced $catalogSearchAdvanced
     * @param \Magento\Catalog\Model\Layer $catalogLayer
     * @param \Magento\UrlFactory $urlFactory
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\CatalogSearch\Model\Advanced $catalogSearchAdvanced,
        \Magento\Catalog\Model\Layer $catalogLayer,
        \Magento\UrlFactory $urlFactory,
        array $data = array()
    ) {
        $this->_catalogSearchAdvanced = $catalogSearchAdvanced;
        $this->_catalogLayer = $catalogLayer;
        $this->_urlFactory = $urlFactory;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {
            $breadcrumbs->addCrumb('home', array(
                'label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link'  => $this->_storeManager->getStore()->getBaseUrl(),
            ))->addCrumb('search', array(
                'label' => __('Catalog Advanced Search'),
                'link'  => $this->getUrl('*/*/')
            ))->addCrumb('search_result', array(
                'label' => __('Results')
            ));
        }
        return parent::_prepareLayout();
    }

    /**
     * Set order options
     */
    public function setListOrders()
    {
        /* @var $category \Magento\Catalog\Model\Category */
        $category = $this->_catalogLayer->getCurrentCategory();

        $availableOrders = $category->getAvailableSortByOptions();
        unset($availableOrders['position']);

        $this->getChildBlock('search_result_list')->setAvailableOrders($availableOrders);
    }

    /**
     * Set view mode options
     */
    public function setListModes()
    {
        $this->getChildBlock('search_result_list')->setModes(array('grid' => __('Grid'), 'list' => __('List')));
    }

    public function setListCollection()
    {
        $this->getChildBlock('search_result_list')
           ->setCollection($this->_getProductCollection());
    }

    protected function _getProductCollection()
    {
        return $this->getSearchModel()->getProductCollection();
    }

    public function getSearchModel()
    {
        return $this->_catalogSearchAdvanced;
    }

    public function getResultCount()
    {
        if (!$this->getData('result_count')) {
            $size = $this->getSearchModel()->getProductCollection()->getSize();
            $this->setResultCount($size);
        }
        return $this->getData('result_count');
    }

    public function getProductListHtml()
    {
        return $this->getChildHtml('search_result_list');
    }

    public function getFormUrl()
    {
        return $this->_urlFactory->create()
            ->setQueryParams($this->getRequest()->getQuery())
            ->getUrl('*/*/', array('_escape' => true));
    }

    public function getSearchCriterias()
    {
        $searchCriterias = $this->getSearchModel()->getSearchCriterias();
        $middle = ceil(count($searchCriterias) / 2);
        $left = array_slice($searchCriterias, 0, $middle);
        $right = array_slice($searchCriterias, $middle);

        return array('left'=>$left, 'right'=>$right);
    }
}
