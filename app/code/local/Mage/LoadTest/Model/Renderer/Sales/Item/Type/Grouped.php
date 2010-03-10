<?php
/* 
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
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 * 
 * @category   Mage
 * @package    Mage_LoadTest
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_LoadTest_Model_Renderer_Sales_Item_Type_Grouped extends Mage_LoadTest_Model_Renderer_Sales_Item_Type_Abstract {

    public function prepareRequestForCart($_product)
    {
	$this->_product = $_product;
	$this->_typeInstance = $this->_product->getTypeInstance();
	
	$request = array();
	$request['product'] = $this->_product->getId();
	$request['related_product'] = '';
	$groupedQtySum = 0;
	$lastAssociatedId = null;
	foreach($this->_typeInstance->getAssociatedProducts($this->_product) as $product)
	{
	    if($product->isSaleable()) {
		$groupedQty = 1;
		if ($max = $product->getStockItem()->getQty()) {
		    $groupedQty = rand(0, min(10, $max));
		}
		$groupedQtySum += $groupedQty;
		$request['super_group'][$product->getId()] = $groupedQty;
		$lastAssociatedId = $product->getId();
	    }
	}
	if($groupedQtySum == 0 && !is_null($lastAssociatedId))
	    $request['super_group'][$lastAssociatedId]++;
	
	return new Varien_Object($request);
    }
    
}


