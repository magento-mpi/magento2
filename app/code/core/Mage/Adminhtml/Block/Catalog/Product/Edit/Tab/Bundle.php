<?php 
/**
 * Adminhtml catalog product bundle option block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
 class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Bundle extends Mage_Adminhtml_Block_Widget 
 {
 	public function __construct() 
 	{
 		parent::__construct();
 		$this->setTemplate('catalog/product/edit/bundle/options.phtml');
 		$this->setId('bundle_options');
 	}
 	
 } // Class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Bundle end