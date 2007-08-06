<?php
/**
 * Catalog product bundle option link model
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
 class Mage_Catalog_Model_Product_Bundle_Option_Link extends Mage_Core_Model_Abstract 
 {
 	
 	
 	protected function _construct()
 	{
 		$this->_init('catalog/product_bundle_option_link');
 	}
 	
 	public function getDataForSave()
 	{
 		$data = array();
 		$data['product_id'] = $this->getProductId();
 		$data['option_id'] = $this->getOptionId();
 		$data['discount'] = ( $this->getDiscount()=='' ? null : $this->getDiscount() );
 		
 		return $data;
 	}
 } // Class Mage_Catalog_Model_Product_Bundle_Option_Link end