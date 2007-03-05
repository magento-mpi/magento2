<?php



/**
 * Product List block
 *
 * @package    Ecom
 * @module     Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Ecom_Catalog_Block_Product_List extends Ecom_Core_Block_Template_List 
{
	public function __construct() 
	{
		parent::__construct();
    	$this->setViewName('Ecom_Catalog', 'product.list');
	}
}