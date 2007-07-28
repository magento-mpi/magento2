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
	protected $_linkTypeId = 0;
	protected $_productId = 0;
		
	public function __construct() 
    {
        $this->setEntity(Mage::getResourceSingleton('catalog/product'))
           	->setObject('catalog/product_link');
        
    }
    
    public function getConditionForProduct() 
    {
    	return array('product_id'=>$this->getProductId());
    }
    
    public function setProductId($productId) 
    {
    	$this->_productId = $productId;
    	return $this;
    }
    
      
    public function getProductId() 
    {    	
    	return $this->_productId;
    }
       
    public function resetSelect()
    {
    	$result = parent::resetSelect();
    	$this->joinField('link_id', 'catalog/product_link', 'link_id', 'linked_product_id=entity_id', $this->getConditionForProduct(), 'left')
        	->joinField('product_id', 'catalog/product_link', 'product_id', 'linked_product_id=entity_id', $this->getConditionForProduct(), 'left')
        	->joinField('linked_product_id', 'catalog/product_link', 'linked_product_id', 'linked_product_id=entity_id', $this->getConditionForProduct(), 'left')
        	->joinField('link_type_id', 'catalog/product_link', 'link_type_id', 'linked_product_id=entity_id', $this->getConditionForProduct(), 'left')
        	->joinField('link_type', 'catalog/product_link_type', 'code', 'link_type_id=link_type_id', null,'left');
        return $result;
    }
    
   
    public function addLinkAttributeToSelect($code, $linkType=null)
    {
    	 $attribute = $this->_getLinkAttribute($code, $linkType);
   		 $this->joinField($code,  $this->_getLinkAttributeTable($attribute),
   		 				  'value', 'link_id=link_id', 
   		 				  array('product_link_attribute_id'=>$attribute->getId()), 'left');
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
    	$this->_loadLinkTypeId($type);
    	$this->addFieldToFilter('link_type_id', $this->getLinkTypeId());
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
    
    protected function _loadLinkTypeId($type)
    {
    	if($this->_linkAttributeCollectionLoaded && sizeof($this->getLinkAttributeCollection()->getItems()) > 0) {    	
    	   $this->_linkTypeId = current($this->getLinkAttributeCollection()->getItems())->getLinkTypeId();
    	   return $this;
    	}
    	
    	$select = $this->_read->select()
    		->from($this->getLinkAttributeCollection()->getResource()->getTable('product_link_type'),'link_type_id')
    		->where('code = ?', $type);
    	
    	$this->_linkTypeId = $this->_read->fetchOne($select);
    	return $this;
    }
    
    public function getLinkTypeId() 
    {
    	return $this->_linkTypeId;
    }
    
    public function useProductItem()
    {
    	$this->setObject('catalog/product');
    	return $this;
    }
    
    public function load($printQuery=false, $logQuery=false)
    {
    	$result = parent::load($printQuery, $logQuery);
    	if($this->getObject() instanceof Mage_Catalog_Product_Link) {
    		$this->walk('setAttributeCollection', array($this->getLinkAttributeCollection()));
    	}
    	return $result;
    }
    
    public function getColumnValues($colName)
    {
        $col = array();
        foreach ($this->getItems() as $item) {
            $col[] = $item->getData($colName);
        }
        return $col;
    }
    
    public function getItemById($idValue)
    {
        foreach ($this as $item) {
        	if ($item->getId()==$idValue) {
        	    return $item;
        	}
        }
        return false;
    }
    
    public function getItemsByColumnValue($column, $value)
    {
        $res = array();
        foreach ($this as $item) {
        	if ($item->getData($column)==$value) {
        	    $res[] = $item;
        	}
        }
        return $res;
    }
    
    public function getItemByColumnValue($column, $value)
    {
        foreach ($this as $item) {
        	if ($item->getData($column)==$value) {
        	    return $item;
        	}
        }
        return null;
    }
    
}// Class Mage_Catalog_Model_Entity_Product_Link_Collection END