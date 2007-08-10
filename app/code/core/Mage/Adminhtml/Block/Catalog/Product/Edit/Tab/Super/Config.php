<?php
/**
 * Adminhtml catalog super product configurable tab
 *
 * @package    Mage
 * @subpackage Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config extends Mage_Adminhtml_Block_Widget
{
	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('catalog/product/edit/super/config.phtml');
		$this->setId('config_super_product');
	}
	
	public function getAttributesJson()
	{
		$attributes = Mage::registry('product')->getSuperAttributes();
		if(!$attributes) {
			return '[]';
		}
		return Zend_Json::encode($attributes);
	}
	
	public function getLinksJson()
	{
		$links = Mage::registry('product')->getSuperLinks();
		if(!$links) {
			return '{}';
		}
		return Zend_Json::encode($links);
	}
	
	protected function _initChildren()
	{
		$this->setChild('grid', 
			$this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_super_config_grid')
		);
	}
	
	protected function getGridHtml()
	{
		return $this->getChildHtml('grid');
	}
	
	protected function getGridJsObject()
	{
		return $this->getChild('grid')->getJsObjectName();
	}
	
}// Class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config END