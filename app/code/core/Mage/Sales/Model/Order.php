<?php

class Mage_Sales_Model_Order extends Varien_Data_Object 
{
    protected $_addresses = null;
    protected $_items = null;
    protected $_attributes = null;
    protected $_payments = null;

    public function getAddresses()
    {
        if (is_null($this->_addresses)) {
            $this->_addresses = Mage::getModel('sales', 'order_address_collection');
        }
        return $this->_addresses;
    }
    
    public function getItems()
    {
        if (is_null($this->_items)) {
            $this->_items = Mage::getModel('sales', 'order_item_collection');
        }
        return $this->_items;
    }
    
    public function getAttributes()
    {
        if (is_null($this->_attributes)) {
            $this->_attributes = Mage::getModel('sales', 'order_attribute_collection');
        }
        return $this->_attributes;
    }
    
    public function getPayments()
    {
        if (is_null($this->_payments)) {
            $this->_payments = Mage::getModel('sales', 'order_payment_collection');
        }
        return $this->_payments;
    }
    
    public function getAddressByType($type)
    {
        foreach ($this->getAddresses()->getItems() as $addr) {
            if ($addr->getAddressTypeCode()==$type) {
                return $addr;
            }
        }
    }
    
    public function collectTotals($type='')
    {
        $attrClasses = Mage::getConfig()->getGlobalCollection('salesOrderAttributes');
        $totalsArr = array();
        foreach ($this->getAttributes()->getItems() as $item) {
            if ($item->getEntityType()!='order') {
                continue;
            }
            $code = $item->getAttributeCode();
            if (!$attrClasses->$code) {
                continue;
            }
            $className = $attrClasses->$code->getClassName();
            $attr = new $className();
            $arr = $attr->collectTotals($order);
            foreach ($arr as $i=>$row) {
                if (''!==$type && $row['type']!==$type || '_output'===$type && empty($row['output'])) {
                    unset($arr[$i]);
                }
            }
            $totalsArr = array_merge_recursive($totalsArr, $arr);
        }
        return $totalsArr;
    }
    
    public function getItemsAsArray($fields=null)
    {
        $arr = array();
        if (is_null($fields)) {
            foreach ($this->getItems() as $item) {
                $arr[$item->getOrderItemId()] = $item->getData();
            }
            foreach ($this->getAttributes()->getByEntity('item') as $attr) {
                $arr[$attr->getEntityId()][$attr->getAttributeCode()] = $attr->getData();
            }
        } else {
            foreach ($this->getItems() as $item) {
                $arr[$item->getOrderItemId()] = $item->getData();
                foreach ($fields as $fieldName=>$fieldType) {
                    $attrs = $this->getAttributes()->getByEntity('item', $fieldName);
                    $arr[$item->getOrderItemId()][$fieldName] = $attrs[0]->getData('attribute_'.$fieldType);  
                }
            }
        }
        return $arr;
    }
    
    public function addCustomerAddress(Mage_Customer_Model_Address $source, $type)
    {
        $address = Mage::getModel('sales', 'order_address');
        
        $fields = array(
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
            'fax'=>'text'
        );
        foreach ($fields as $fieldName=>$fieldType) {
            $attr = Mage::getModel('sales', 'order_attribute');
            $attr->setEntityType('address');
            $attr->setAttributeCode($fieldName);
            $attr->setData('attribute_'.$fieldType, $source->getData($fieldName));
            $address->addAttribute($attr);
        }
        
        $this->getAddresses()->addItem($address);
        
        return $this;
    }
    
    public function addProductItem(Mage_Catalog_Model_Product $source, $qty=1)
    {
        $item = Mage::getModel('sales', 'order_item');
        
        $fields = array(
            'product_id'=>'int', 
            'product_name'=>'text', 
        );
        foreach ($fields as $fieldName=>$fieldType) {
            $attr = Mage::getModel('sales', 'order_attribute');
            $attr->setEntityType('item');
            $attr->setAttributeCode($fieldName);
            $attr->setData('attribute_'.$fieldType, $source->getData($fieldName));
            $item->addAttribute($attr);
        }
        
        $this->getItems()->addItem($item);
        
        return $this;
    }
    
    /**
     * Add payment to order
     * 
     * @todo Maybe not to use this, but to do directly $order->getPayments()->addItem($source)
     *
     * @param Mage_Sales_Model_Payment $source
     */
    public function addPayment(Mage_Sales_Model_Payment $source)
    {
        $payment = Mage::getModel('sales', 'order_payment');
        
        $fields = array();
        
        foreach ($fields as $fieldName=>$fieldType) {
            $attr = Mage::getModel('sales', 'order_attribute');
            $attr->setEntityType('payment');
            $attr->setAttributeCode($fieldName);
            $attr->setData('attribute_'.$fieldType, $source->getData($fieldName));
            $item->addAttribute($attr);
        }
    }
}