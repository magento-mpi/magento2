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
 	protected  $_bundleOptionCollection = null;
 	
 	public function __construct() 
 	{
 		parent::__construct();
 		$this->setTemplate('catalog/product/edit/bundle/options.phtml');
 		$this->setId('bundle_options');
 	}
 	
 	public function getJsObjectName() 
 	{
 		return uc_words($this->getId(), '').'JsObject';
 	}
 	
 	public function getTabJsObjectName() 
 	{
 		return uc_words($this->getId(), '').'TabJsObject';
 	}
 	
 	public function getJsTemplateHtmlId() 
 	{
 		return $this->getId().'_option_new_template';
 	}
 	
 	public function getJsContainerHtmlId() 
 	{
 		return $this->getId().'_option_container';
 	}
 	
 	protected function _initChildren() 
 	{
 		$this->setChild('option_form', 
 			$this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_bundle_option')
 				->setParent($this)
 		);
 		
 		return $this;
 	}
 	
 	public function getBundleOptions() 
 	{
 		if(is_null($this->_bundleOptionCollection)) {
 			$this->_bundleOptionCollection = Mage::registry('product')->getBundleOptionCollection()
 				->setOrder('position', 'asc')
 				->load();
 		}
 		
 		return $this->_bundleOptionCollection;
 	}
 	
 	public function getOptionProductsJSON($option) 
 	{
 		$data = $option->getLinkCollection()->toArray();
 		
 		if(sizeof($data)==0) {
 			return '{}';
 		}
 		return Zend_Json_Encoder::encode($data);
 	}
 	
 	public function getEscaped($value) 
 	{
 		return addcslashes($value, "\\'\n\r");
 	}
} // Class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Bundle end