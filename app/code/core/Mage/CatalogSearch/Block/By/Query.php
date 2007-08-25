<?php

/**
 * Product search result block
 *
 * @package    Mage
 * @module     Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_CatalogSearch_Block_By_Query extends Mage_Core_Block_Template
{
	protected $_productCollection;
	
    public function __construct()
    {
        parent::__construct();
		$this->setTemplate('catalog/search/result.phtml');
    }

    protected function _initChildren()
    {
        $queryEscaped = $this->htmlEscape($this->getQuery());

        // add Home breadcrumb
    	$this->getLayout()->getBlock('breadcrumbs')
            ->addCrumb('home',
                array('label'=>__('Home'),
                    'title'=>__('Go to Home Page'),
                    'link'=>Mage::getBaseUrl())
                );
        
        $title = __('Search results for:').' '.$queryEscaped;
        $this->getLayout()->getBlock('head')->setTitle($title);
        $this->getLayout()->getBlock('root')->setHeaderTitle($title);   

        $resultBlock = $this->getLayout()->createBlock('catalog/product_list', 'product_list')
            ->setAvailableOrders(array('name'=>__('Name'), 'price'=>__('Price')))
            ->setModes(array('list' => __('List')))
            ->setCollection($this->_getProductCollection());
        $this->setChild('search_result_list', $resultBlock);
    }

    public function getProductListHtml()
    {
        return $this->getChildHtml('search_result_list');
    }
    
    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getProductCollection()
    {
    	if (is_null($this->_productCollection)) {
	        $this->_productCollection = Mage::getResourceModel('catalog/product_collection')
	            ->addAttributeToSelect('name')
	            ->addAttributeToSelect('price')
	            ->addAttributeToSelect('description')
	            ->addAttributeToSelect('image')
	            ->addAttributeToSelect('small_image')
	            ->addSearchFilter($this->getQuery());
    	}
        
        return $this->_productCollection;
    }
    
    public function getResultCount()
    {
    	return $this->_getProductCollection()->getSize();
    }
}