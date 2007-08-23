<?php
/**
 * Catalog super product configurable part block
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
 class Mage_Catalog_Block_Product_View_Super_Config extends Mage_Core_Block_Template 
 {
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/product/view/super/config.phtml');
    }
    
    public function getAllowAttributes()
    {
        return $this->getProduct()->getSuperAttributes(false);
    }
    
    public function getAllowProducts()
    {
        return $this->getProduct()->getSuperLinks();
    }
    
    public function getJsonConfig()
    {
        $attributes = array();
        $options = array();
        $store = Mage::getSingleton('core/store');
        
        foreach ($this->getAllowProducts() as $productId => $productAttributes) {
        	foreach ($productAttributes as $attribute) {
        	    if (!isset($options[$attribute['attribute_id']])) {
        	        $options[$attribute['attribute_id']] = array();
        	    }
        	    
        	    if (!isset($options[$attribute['attribute_id']][$attribute['value_index']])) {
        	        $options[$attribute['attribute_id']][$attribute['value_index']] = array();
        	    }
        	    $options[$attribute['attribute_id']][$attribute['value_index']][] = $productId;
        	}
        }
        
        foreach ($this->getAllowAttributes() as $attribute) {
            $attributeId = $attribute['attribute_id'];
        	$info = array(
        	   'id'        => $attributeId,
        	   'code'      => $attribute['attribute_code'],
        	   'label'     => $attribute['label'],
        	   'options'   => array()
        	);
        	
        	foreach ($attribute['values'] as $value) {
        		//$info['options'][$value['value_index']] = array(
        		$info['options'][] = array(
        		    'id'    => $value['value_index'],
                    'label' => $value['label'],
                    'price' => $this->_preparePrice($value['pricing_value'], $value['is_percent']),
                    'products'   => isset($options[$attributeId][$value['value_index']]) ? $options[$attributeId][$value['value_index']] : array(),
        		);
        	}
        	
        	$attributes[$attribute['attribute_id']] = $info;
        }
        
        $config = array(
            'attributes'=> $attributes,
            'template'  => str_replace('%s', '#{price}', $store->getCurrentCurrency()->getOutputFormat()),
            'basePrice' => $this->_preparePrice($this->getProduct()->getFinalPrice()),
            'productId' => $this->getProduct()->getId(),
            'chooseText'=> __('Choose option...'),
        );
        
        return Zend_Json::encode($config);
    }
    
    protected function _preparePrice($price, $isPercent=false)
    {
        if ($isPercent) {
            $price = $this->getProduct()->getFinalPrice()*$price/100;
        }
        
        return number_format($price, 2);
    }
    
    /**
     * REtrieve current product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('product');
    }
    
 	/*public function getAttributes()
 	{
 		if($this->getRequest()->getParam('super_attribute') && is_array($this->getRequest()->getParam('super_attribute'))) {
 			foreach ($this->getRequest()->getParam('super_attribute') as $attributeId=>$attributeValue) {
 				if(!empty($attributeValue) && $attribute = Mage::registry('product')->getResource()->getAttribute($attributeId)) {
 					Mage::registry('product')->getSuperLinkCollection()
 						->addFieldToFilter($attribute->getAttributeCode(), $attributeValue);
 				}
 			}
 		}
 		
 		return Mage::registry('product')->getSuperAttributes(false, true);
 	}*/
 	
 	public function canDisplayContainer()
 	{
 		return !(bool)$this->getRequest()->getParam('ajax', false);
 	}
 	
 	public function getPricingValue($value)
    {
    	$value = Mage::registry('product')->getPricingValue($value);
    	$numberSign = $value >= 0 ? '+' : '-';
    	return ' ' . $numberSign . ' ' . Mage::getSingleton('core/store')->formatPrice(abs($value));
    }
    
    public function isSelectedOption($value, $attribute) 
    {
    	$selected = $this->getRequest()->getParam('super_attribute', array());
    	if(is_array($selected) && isset($selected[$attribute['attribute_id']]) && $selected[$attribute['attribute_id']]==$value['value_index']) {
    		return true;
    	}
    	
    	return false;
    }
    
    public function getUpdateUrl()
    {
    	return $this->getUrl('*/*/superConfig', array('_current'=>true));    	
    }
    
    public function getUpdatePriceUrl()
    {
    	return $this->getUrl('*/*/price', array('_current'=>true));
    }
 } // Class Mage_Catalog_Block_Product_View_Super_Config end