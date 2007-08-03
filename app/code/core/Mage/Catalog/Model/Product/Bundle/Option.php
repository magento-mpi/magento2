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
 } // Class Mage_Catalog_Model_Product_Bundle_Option end