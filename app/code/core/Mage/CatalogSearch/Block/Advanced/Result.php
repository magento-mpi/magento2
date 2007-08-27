<?php
/**
 * Advanced search result
 *
 * @package     Mage
 * @subpackage  CatalogSearch
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_CatalogSearch_Block_Advanced_Result extends Mage_Catalog_Block_Product_List
{
    protected function _initChildren()
    {
        parent::_initChildren();
        
    	$this->getLayout()->getBlock('breadcrumbs')
            ->addCrumb('home',
                array('label'=>__('Home'),
                    'title'=>__('Go to Home Page'),
                    'link'=>Mage::getBaseUrl())
                )
            ->addCrumb('search',
                array('label'=>__('Catalog Advanced Search'), 'link'=>$this->getUrl('*/*/'))
                )
            ->addCrumb('search_result',
                array('label'=>__('Results'))
                );
        return $this;
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
            	->addAttributeToSelect('url_key')
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('price')
                ->addAttributeToSelect('description')
                ->addAttributeToSelect('image')
                ->addAttributeToSelect('small_image');
                
            $this->_addFilters();
        }
        return parent::_getProductCollection();
    }
    
    protected function _addFilters()
    {
        $attributes = $this->getSearchModel()->getAttributes();
        $values = $this->getRequest()->getQuery();
        
        foreach ($attributes as $attribute) {
        	$code      = $attribute->getAttributeCode();
        	$condition = false;
        	
        	if (isset($values[$code])) {
        	    $value = $values[$code];
        	    if (is_array($value)) {
        	        if ((isset($value['from']) && strlen($value['from']) > 0) || (isset($value['to']) && strlen($value['to']) > 0)) {
        	            $condition = $value;
        	        }
        	        elseif(!isset($value['from']) && !isset($value['to'])) {
        	            if ($attribute->getBackend()->getType() == 'int') {
        	                $condition = array('in'=>$value);
        	            }
        	        }
        	    }
        	    else {
        	        if (strlen($value)>0) {
        	            if (in_array($attribute->getBackend()->getType(), array('varchar', 'text'))) {
        	                $condition = array('like'=>'%'.$value.'%');
        	            }
        	            else {
        	                $condition = $value;
        	            }
        	        }
        	    }
        	}
        	
        	if ($condition) {
        	    $this->_getProductCollection()->addFieldToFilter($code, $condition);
        	}
        }
        return $this;
    }
    
    public function getSearchModel()
    {
        return Mage::getSingleton('catalogsearch/advanced');
    }
}
