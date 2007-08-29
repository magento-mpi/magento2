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
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog comapare sidebar block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
 class Mage_Catalog_Block_Product_Compare_Sidebar extends Mage_Core_Block_Template 
 {
 	protected $_items = null;
 	
 	protected function _construct()
 	{
 		$this->setId('compare');
 	}
 	
 	public function getItems()
 	{
 		if(is_null($this->_items)) {
 			$this->_items = Mage::getResourceModel('catalog/product_compare_item_collection')
 				->setStoreId(Mage::getSingleton('core/store')->getId());
 			
 			if(Mage::getSingleton('customer/session')->isLoggedIn()) {
				$this->_items->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId());
			} else {
				$this->_items->setVisitorId(Mage::getSingleton('core/session')->getLogVisitorId());
			}
			
			$this->_items->addAttributeToSelect('name')
				->useProductItem()
				->load();
 		}
 		
 		return $this->_items;
 	}
 	
    public function getRemoveUrl($item)
    {
        return $this->getUrl('catalog/product_compare/remove',array('product'=>$item->getId()));
    }
    
    public function getClearUrl()
    {
        return $this->getUrl('catalog/product_compare/clear');
    }
    
 	public function getCompareUrl()
 	{
 	    $itemIds = array();
 	    foreach ($this->getItems() as $item) {
 	    	$itemIds[] = $item->getId();
 	    }
 	    
 		return $this->getUrl('catalog/product_compare', array('items'=>implode(',', $itemIds)));
 	}
 } // Class Mage_Catalog_Block_Compare_Sidebar end