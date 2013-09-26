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
 * Advanced search result
 *
 * @category   Magento
 * @package    Magento_CatalogSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CatalogSearch_Block_Advanced_Result extends Magento_Core_Block_Template
{
    /**
     * Url factory
     *
     * @var Magento_Core_Model_UrlFactory
     */
    protected $_urlFactory;

    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Catalog layer
     *
     * @var Magento_Catalog_Model_Layer
     */
    protected $_catalogLayer;

    /**
     * Catalog search advanced
     *
     * @var Magento_CatalogSearch_Model_Advanced
     */
    protected $_catalogSearchAdvanced;

    /**
     * Construct
     *
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_CatalogSearch_Model_Advanced $catalogSearchAdvanced
     * @param Magento_Catalog_Model_Layer $catalogLayer
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_UrlFactory $urlFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_CatalogSearch_Model_Advanced $catalogSearchAdvanced,
        Magento_Catalog_Model_Layer $catalogLayer,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_UrlFactory $urlFactory,
        array $data = array()
    ) {
        $this->_catalogSearchAdvanced = $catalogSearchAdvanced;
        $this->_catalogLayer = $catalogLayer;
        $this->_storeManager = $storeManager;
        $this->_urlFactory = $urlFactory;
        parent::__construct($coreData, $context, $data);
    }

    protected function _prepareLayout()
    {
        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbs->addCrumb('home', array(
                'label'=>__('Home'),
                'title'=>__('Go to Home Page'),
                'link' => $this->_storeManager->getStore()->getBaseUrl(),
            ))->addCrumb('search', array(
                'label'=>__('Catalog Advanced Search'),
                'link'=>$this->getUrl('*/*/')
            ))->addCrumb('search_result', array(
                'label'=>__('Results')
            ));
        }
        return parent::_prepareLayout();
    }

    public function setListOrders() {
        $category = $this->_catalogLayer->getCurrentCategory();
        /* @var $category Magento_Catalog_Model_Category */

        $availableOrders = $category->getAvailableSortByOptions();
        unset($availableOrders['position']);

        $this->getChildBlock('search_result_list')
            ->setAvailableOrders($availableOrders);
    }

    public function setListModes() {
        $this->getChildBlock('search_result_list')
            ->setModes(array(
                'grid' => __('Grid'),
                'list' => __('List'))
            );
    }

    public function setListCollection() {
        $this->getChildBlock('search_result_list')
           ->setCollection($this->_getProductCollection());
    }

    protected function _getProductCollection(){
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
