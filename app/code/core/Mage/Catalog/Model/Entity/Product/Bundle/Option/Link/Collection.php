<?php
/**
 * Catalog product bundle option link collection
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
 class Mage_Catalog_Model_Entity_Product_Bundle_Option_Link_Collection extends Mage_Catalog_Model_Entity_Product_Collection 
 {
 	protected $_optionIds = array();
 	protected $_storeId = 0;
 	
 	public function __construct() 
    {
        $this->setEntity(Mage::getResourceSingleton('catalog/product'))
           	->setObject('catalog/product_bundle_option_link');
        
    }
    
    public function getJoinCondition()
    {
    	if(sizeof($this->getOptionIds())==0) {
    		return null;
    	}
    	
    	return $this->_read->quoteInto('{{table}}.option_id in (?)', $this->getOptionIds());
    }
    
         	
    public function setOptionId($id)
    {
    	$this->_optionIds = array($id);
    	return $this;
    }
    
    public function setOptionIds(array $ids)
    {
    	$this->_optionIds = $ids;
    	return $this;
    }
    
    public function getOptionIds()
    {
    	return $this->_optionIds;
    }
    
    public function setStoreId($storeId)
    {
    	$this->_storeId = $storeId;
    	$this->_joinLinkTable();
    	return $this;
    }
    
    public function getStoreId()
    {
    	return (int)$this->_storeId;
    }
        
    protected function _joinLinkTable()
    {
    	$table = 'catalog/product_bundle_option_link';
    	$this->joinField('link_id', $table, 'link_id', 'product_id=entity_id', $this->getJoinCondition(), 'left')
    		->joinField('product_id', $table, 'product_id', 'link_id=link_id', null, 'left')
    		->joinField('option_id', $table, 'option_id', 'link_id=link_id', null, 'left')
    		->joinField('discount', $table, 'discount', 'link_id=link_id', null, 'left')
    		->joinField('store_id', 
                    'catalog/product_store', 
                    'store_id', 
                    'product_id=entity_id', 
                    '{{table}}.store_id='.(int)$this->getStoreId());
    	return $this;
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
 } // Class Mage_Catalog_Model_Entity_Product_Bundle_Option_Link_Collection end