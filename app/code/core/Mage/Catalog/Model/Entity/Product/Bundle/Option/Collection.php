<?php
/**
 * Catalog product bundle option collection
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
 class Mage_Catalog_Model_Entity_Product_Bundle_Option_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract 
 {
 	protected $_linkCollection = null;
 	protected $_storeId = 0;
 	
 	protected function _construct()
 	{
 		$this->_init('catalog/product_bundle_option');
 	}
 	
 	public function setStoreId($storeId)
    {
    	$this->_storeId = $storeId;
    	$this->_joinValues();
    	return $this;
    }
    
    public function getStoreId()
    {
    	return (int)$this->_storeId;
    }
    
    public function setProductIdFilter($productId)
    {
    	$this->getSelect()
    		->where('main_table.product_id = ?', $productId);    	
    	return $this;
    }
     
 	 	
    protected function _joinValues()
    {
    	$this->getSelect()
    		->join(array('value'=>$this->getTable('product_bundle_option_value')), 'value.option_id=main_table.option_id AND value.store_id='.(int)$this->getStoreId(), array('label', 'position', 'store_id'));
    }
    
 	protected function _loadLinks()
 	{
 		$optionsIds = $this->getColumnValues('option_id');
 		
 		if(sizeof($optionsIds)==0) {
 			return $this;
 		}
		$this->getLinkCollection()
			->setOptionIds($optionsIds)
			->addFieldToFilter('option_id', array('notnull'=>1))
			->load();
			
		foreach($this->getItems() as $item) {
			foreach ($this->getLinkCollection() as $link) {
				$item->getLinkCollection()->addItem($link);
			}
		}
		
 		return $this;
 	}
 	
 	public function getLinkCollection()
 	{
 		if(is_null($this->_linkCollection)) {
 			$this->_linkCollection = Mage::getResourceModel('catalog/product_bundle_option_link_collection')
 				->setStoreId($this->getStoreId());
 		}
 		
 		return $this->_linkCollection;
 	}
 	
 	public function load($printQuery=false, $logQuery=false) {
 		parent::load($printQuery, $logQuery);
 		$this->_loadLinks();
 		return $this;
 	}
 } // Class Mage_Catalog_Model_Entity_Product_Bundle_Option_Collection end