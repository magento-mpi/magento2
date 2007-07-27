<?php
/**
 * Catalog product links collection
 *
 * @package    Mage
 * @subpackage Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Catalog_Model_Entity_Product_Link_Collection extends Mage_Catalog_Model_Entity_Product_Collection
{
	protected $_linkAttributeCollection = null;
	protected $_linkAttributeCollectionLoaded = false;
	
	public function __construct() 
    {
        $this->setEntity(Mage::getResourceSingleton('catalog/product'))
           	->setObject('catalog/product_link');
        
    }
    
    public function resetSelect()
    {
    	$result = parent::resetSelect();
    	$this->joinField('link_id', 'catalog/product_link', 'link_id', 'linked_product_id=entity_id')
        	->joinField('product_id', 'catalog/product_link', 'product_id', 'linked_product_id=entity_id')
        	->joinField('link_type_id', 'catalog/product_link', 'link_type_id', 'linked_product_id=entity_id')
        	->joinField('link_type', 'catalog/product_link_type', 'code', 'link_type_id=link_type_id');
        return $result;
    }
    
   
    public function addLinkAttributeToSelect($code, $linkType=null)
    {
    	 $attribute = $this->_getLinkAttribute($code, $linkType);
   		 $this->joinField($code,  $this->_getLinkAttributeTable($attribute), 
   		 				  'value', 'link_id=link_id', 
   		 				  array('product_link_attribute_id'=>$attribute->getId()));
   		 return $this;
    }
    
    public function getLinkAttributeCollection()
    {
    	if(is_null($this->_linkAttributeCollection)) {
    		$this->_linkAttributeCollection = Mage::getResourceModel('catalog/product_link_attribute_collection');
    	}
    	
    	return $this->_linkAttributeCollection;
    }
    
    protected function _getLinkAttribute($code, $linkType=null)
    {
    	$this->_loadLinkAttributes();
    	
    	if(!($attribute = $this->getLinkAttributeCollection()->getItemByCodeAndLinkType($code, $linkType))) {
    		Mage::throwException('Invalid Attribute Requested');
    	}
    	    	
    	return $attribute;
    }
    
    protected function _getLinkAttributeTable(Varien_Object $attribute)
    {
    	return 'catalog/product_link_attribute_' . $attribute->getDataType();
    }
    
    public function addLinkTypeFilter($type)
    {
    	$this->_loadLinkAttributes($type);
    	$this->addFieldToFilter('link_type', $type);
    	return $this;
    }
        
    protected function _loadLinkAttributes($type=null)
    {
    	if(!$this->_linkAttributeCollectionLoaded) {
    		$this->getLinkAttributeCollection()->addLinkTypeData();
    		
    		if(!is_null($type)) {
    			$this->getLinkAttributeCollection()->addFieldToFilter('link_type', $type);
    		}
    		
    		$this->getLinkAttributeCollection()->load();
    		$this->_linkAttributeCollectionLoaded = true;
    	}
    	    	
    	return $this;
    }
    
    public function useProductItem()
    {
    	$this->setObject('catalog/product');
    	return $this;
    }
}// Class Mage_Catalog_Model_Entity_Product_Link_Collection END