<?php
/**
 * Catalog product price block
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
 class Mage_Catalog_Block_Product_View_Price extends Mage_Core_Block_Template 
 {
 	public function getPrice()
 	{
 		$product = Mage::registry('product');
 		if($product->isSuperConfig()) {
 			$price = $product->getCalculatedPrice((array)$this->getRequest()->getParam('super_attribute', array()));
 			return Mage::getSingleton('core/store')->formatPrice($price);
 		}
 		
 		return $product->getFormatedPrice();
 	}
 } // Class Mage_Catalog_Block_Product_View_Price end