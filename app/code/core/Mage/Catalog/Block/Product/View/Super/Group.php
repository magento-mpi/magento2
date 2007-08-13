<?php
/**
 * Catalog super product group block
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
 class Mage_Catalog_Block_Product_View_Super_Group extends Mage_Core_Block_Template 
 {
 	protected $_filter = null;
 	
 	public function getItems()
 	{
 		return Mage::registry('product')->getSuperGroupProductsLoaded();
 	}
 	
 	public function filterQty($qty) 
 	{
 		if(empty($qty)) {
 			return '';
 		}
 		return $this->getFilter()->filter($qty);
 	}
 	
 	public function getFilter()
 	{
 		if(is_null($this->_filter)) {
 			$this->_filter = new Zend_Filter_Int();
 		}
 		
 		return $this->_filter;
 	}
 } // Class Mage_Catalog_Block_Product_View_Super_Group end