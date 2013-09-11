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
namespace Magento\CatalogSearch\Block\Advanced;

class Result extends \Magento\Core\Block\Template
{
    protected function _prepareLayout()
    {
        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbs->addCrumb('home', array(
                'label'=>__('Home'),
                'title'=>__('Go to Home Page'),
                'link'=>Mage::getBaseUrl()
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
        $category = \Mage::getSingleton('Magento\Catalog\Model\Layer')
            ->getCurrentCategory();
        /* @var $category \Magento\Catalog\Model\Category */

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
        return \Mage::getSingleton('Magento\CatalogSearch\Model\Advanced');
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
        return \Mage::getModel('Magento\Core\Model\Url')
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
