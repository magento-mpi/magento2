<?php
/**
 * Product data validation class
 * 
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Validate_Product extends Mage_Core_Validate 
{
    /**
     * Data validation
     */
    public function isValid() 
    {
        $this->_data = $this->_prepareArray($this->_data, array('product_id', 'attribute_set_id', 'attribute'));
        $validateSetId = $this->_getValidator('int');

        if (!$validateSetId->isValid($this->_data['attribute_set_id'])) {
            $this->_message = 'Empty attribute set ID';
            return false;
        }
        
        // Validate attributes
        $attributeSetModel = Mage::getModel('catalog', 'product_attribute_set');
        $attributes = $attributeSetModel->getAttributesInfo($this->_data['attribute_set_id']);

        foreach ($attributes as $attribute) {
            
            // Init attribute validator
            $attributeValidate = new Zend_Validate();
            if ($attribute['required']) {
                if (!isset($this->_data['attribute'][$attribute['attribute_id']])) {
                    $this->_message = 'Required attribute "' . $attribute['attribute_code'] . '" is not defined';
                    return false;
                }
                $attributeValidate->addValidator(new Zend_Validate_StringLength(1));
            }
            
            if (!$attributeValidate->isValid($this->_data['attribute'][$attribute['attribute_id']])) {
                $this->_message = $attribute['attribute_code'] . ': validation error';
                return false;
            }
        }
        return true;
    }
    
    public function getProductId()
    {
        if (!empty($this->_data['product_id'])) {
            return (int) $this->_data['product_id'];
        }
        return false;
    }
}
