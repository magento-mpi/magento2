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
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Site Map category block
 *
 * @category   Mage
 * @package    Mage_Catalog_Block_Sitemap_Product
 * @module     Catalog
 * @author     Lindy Kyaw <lindy@varien.com>
 */
class Mage_Catalog_Block_Sitemap_Product extends Mage_Catalog_Block_Sitemap_Abstract
{		 
	public function __construct()
	{
		parent::__construct();
		$collection = Mage::getResourceModel('catalog/product_collection') //Mage_Catalog_Model_Entity_Product_Collection
				 	->addAttributeToSelect('name')
				 	->addAttributeToSort('name');
				 	
        Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        	 	
		$collection->getEntity()->setStore(Mage::app()->getStore());

        $this->setMapItemCollection($collection);
	}  	
	
	public function getMyPageTitle()
	{
		return $this->__('Products');
	}
	
	public function getMyUrl($obj)
	{
		return $obj->getProductUrl();
	}	
	
	public function getMyOtherPageTitle()
	{
		return $this->__('Categories Sitemap');
	}
	
	public function getMyOtherPageUrl()
	{
		return  Mage::helper('catalog/map')->getCategoryUrl();
	}
	
}