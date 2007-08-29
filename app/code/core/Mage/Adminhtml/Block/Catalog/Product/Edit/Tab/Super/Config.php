<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml catalog super product configurable tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
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