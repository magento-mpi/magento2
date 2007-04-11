<?php

class Mage_Sales_Model_Quote_Entity extends Varien_Data_Object
{
    protected $_attributes = array();
    protected $_quote = null;
    
    public function setQuote($quote)
    {
        $this->_quote = $quote;
    }
    
    public function getQuote()
    {
        return $this->_quote;
    }
    
    public function getAttributes()
    {
        return $this->_attributes;
    }
        
    public function importDataObject(Varien_Data_Object $source, $type='')
    {
        if (is_null($source)) {
            return $this;
        }
        if (''===$type) {
            $type = $this->getEntityType();
            if (!$type) {
                throw new Mage_Sales_Exception("Entity type is not specified.");
            }
            $fields = $this->getDefaultType($type);
            if (empty($fields)) {
                throw new Mage_Sales_Exception("Invalid entity type.");
            }
        }
        
        if ($source->hasQuoteEntityId()) {
            $this->setQuoteEntityId($source->getQuoteEntityId());
        }
        
        if ($source->hasQuoteId()) {
            $this->setQuoteId($source->getQuoteId());
        }
        if (''!==$type) {
            $this->setEntityType($type);
        }
        $fields = $this->getDefaultAttributeType('', $type);
        
        foreach ($fields as $fieldName=>$fieldType) {
            $this->setAttribute($fieldName.'/'.$fieldType, $source->getData($fieldName));
        }
        return $this;
    }
    
    /**
     * Set attributes
     *
     * @param string|array|Mage_Sales_Model_Quote_Attribute $var
     * @param mixed $value
     * @param boolean $isChanged
     * @return mixed
     */
    public function setAttribute($var, $value=null, $isChanged=true)
    {
        if ($var instanceof Mage_Sales_Model_Quote_Attribute) {
            $this->_attributes[$var->getAttributeCode()] = $var;
            return $this;
        }
        
        list($attrCode, $type) = explode('/', $var);
        if (empty($type)) {
            $type = $this->getDefaultAttributeType($attrCode);
        }
        if (empty($type)) {
            throw new Mage_Sales_Exception("No type was specified for ".$var." and default type was not found.");
        }
        if (!isset($this->_attributes[$attrCode])) {
            $attr = Mage::getModel('sales', 'quote_attribute');
            $attr->setEntity($this);
        }
        $attr->setAttributeCode($attrCode);
        $attr->setData('attribute_'.$type, $value, $isChanged);
        $this->_attributes[$attrCode] = $attr;
        $this->_quote->getAttributes()->addItem($attr);
        return $this;
    }
    
    public function getAttribute($var)
    {
        list($attrCode, $type) = explode('/', $var);
        if (!isset($type)) {
            $type = $this->getDefaultAttributeType($attrCode);
            if (false===$type) {
                throw new Mage_Sales_Exception("No type was specified for ".$var." and default type was not found.");
            }
        }
        if (!isset($this->_attributes[$attrCode])) {
            return false;
        }
        return $this->_attributes[$attrCode]->getData('attribute_'.$type);
    }
    
    public function getDefaultAttributeType($attributeName='', $entityType='')
    {
        static $types = array(
            'quote'=>array(
                'status'=>'int',
                'discount'=>'decimal',
                'grand'=>'decimal',
                'subtotal'=>'decimal',
                'tax'=>'decimal',
            ),
            'address'=>array(
                'firstname'=>'text', 
                'lastname'=>'text', 
                'company'=>'text', 
                'street'=>'text', 
                'city'=>'text', 
                'region'=>'text', 
                'region_id'=>'int', 
                'postcode'=>'text', 
                'country_id'=>'int', 
                'telephone'=>'text', 
                'fax'=>'text',
            ),
            'item'=>array(
                'product_id'=>'int',
                'model'=>'varchar',
                'image'=>'text',
                'name'=>'text',
                'price'=>'decimal',
                'tier_price'=>'decimal',
                'qty'=>'decimal',
                'discount'=>'decimal',
                'tax'=>'decimal',
                'row_total'=>'decimal',
                'weight'=>'decimal',
                'cost'=>'decimal',
            ),
            'payment'=>array(
                'amount'=>'decimal',
                'type'=>'varchar',
                'cc_number'=>'varchar',
                'cc_owner'=>'varchar',
                'cc_exp'=>'varchar',
            ),
        );
        
        if (''===$entityType) {
            if (!($entityType = $this->getEntityType())) {
                throw new Mage_Sales_Exception("Entity type is not specified for getDefaultAttributeType in uninitialized entity.");
            }
        }
        
        if (''===$attributeName) {
            if (!isset($types[$entityType])) {
                return false;
            }
            return $types[$entityType];
        }
        
        if (!isset($types[$entityType][$attributeName])) {
            return false;
        }
        
        return $types[$entityType][$attributeName];
    }
}