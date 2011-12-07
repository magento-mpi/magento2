<?php
/* 
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage_LoadTest
 * @copyright  {copyright}
 * @license    {license_link}
 */


class Mage_LoadTest_Model_Renderer_Sales_Item_Type_Simple extends Mage_LoadTest_Model_Renderer_Sales_Item_Type_Abstract {
    
    public function prepareRequestForCart($_product)
    {
	$this->_product = $_product;
	$this->_typeInstance = $this->_product->getTypeInstance();
	$request = array();
	$request['qty'] = $this->_getAllowedQty();
	return new Varien_Object($request);
    }
}


