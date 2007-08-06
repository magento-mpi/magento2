<?php
/**
 * Catalog product bundle option model
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
 class Mage_Catalog_Model_Product_Bundle_Option extends Mage_Core_Model_Abstract 
 {
	protected $_linkCollection = null;
 	
 	protected function _construct() 
	{
		$this->_init('catalog/product_bundle_option');
	}
	
	public function getLinkCollection()
 	{
 		if(is_null($this->_linkCollection)) {
 			$this->_linkCollection = Mage::getResourceModel('catalog/product_bundle_option_link_collection')
 				->setOptionId($this->getId())
 				->setStoreId($this->getStoreId());
 		}
 		
 		return $this->_linkCollection;
 	}
 	
 	public function toArray(array $arrAttributes = array()) 
 	{
 		return $this->getResource()->toArray($this);
 	}
 	
 	public function getDataForSave()
 	{
 		$data = array();
 		$data['product_id'] = $this->getProductId();
 		return $data;
 	}
 	
 	public function getPosition() {
 		if(strlen($this->getData('position')) > 0 || $this->getData('position')) {
 			return $this->getData('position');
 		}
 		return $this->getDefaultPosition();
 	}
 	
 	public function getLabel() {
 		if(strlen($this->getData('label')) > 0 || $this->getData('label')) {
 			return $this->getData('label');
 		}
 		return $this->getDefaultLabel();
 	}
 } // Class Mage_Catalog_Model_Product_Bundle_Option end