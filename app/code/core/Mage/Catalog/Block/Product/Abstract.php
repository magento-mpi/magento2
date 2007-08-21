<?php
/**
 * Product abstract block
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
abstract class Mage_Catalog_Block_Product_Abstract extends Mage_Core_Block_Template
{
	public function getAddToCartUrl($product)
	{
	    return $this->getUrl('checkout/cart/add',array('product'=>$product->getId()));
	}
	
	public function getAddToWishlistUrl($product)
	{
	    return $this->getUrl('wishlist/index/add',array('product'=>$product->getId()));
	}
}
