<?php



/**
 * Product List block
 *
 * @package    Mage
 * @module     Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Block_Product_List extends Mage_Core_Block_Template_List 
{
	public function __construct() 
	{
		parent::__construct();
    	$this->setViewName('Mage_Catalog', 'product.list');
	}
}