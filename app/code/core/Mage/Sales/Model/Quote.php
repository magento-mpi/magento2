<?php

class Mage_Sales_Model_Quote extends Mage_Sales_Model_Document 
{
    protected function _setDocumentProperties()
    {
        $this->_docType = 'quote';
    }
    
    public function getEntityTemplates()
    {
        return array(
            'item'=>Mage::getModel('sales', 'quote_entity_item'),
            'address'=>Mage::getModel('sales', 'quote_entity_address'),
            'payment'=>Mage::getModel('sales', 'quote_entity_payment'),
            'shipping'=>Mage::getModel('sales', 'quote_entity_shipping'),
        );
    }
    
    public function getItems()
    {
        return $this->getEntitiesByType('item');
    }

    public function getAddressByType($type)
    {
        foreach ($this->getEntitiesByType('address') as $addr) {
            if ($addr->getAddressType()==$type) {
                return $addr;
            }
        }
        return false;
    }
    
    public function setBillingAddress(Varien_Data_Object $address)
    {
        $old = $this->getAddressByType('billing');
        if (!empty($old)) {
            $this->removeEntity($old);
        }
        $address->setAddressType('billing');
        $this->addEntity($address);
        
        return $this;
    }
    
    public function setShippingAddress(Varien_Data_Object $address)
    {
        $old = $this->getAddressByType('shipping');
        if (!empty($old)) {
            $this->removeEntity($old);
        }
        $address->setAddressType('shipping');
        $this->addEntity($address);
        return $this;
    }
    
    public function setAddress($addressType, Mage_Customer_Model_Address $address)
    {
        $existingAddress = $this->getAddressByType($addressType);
        if (empty($existingAddress)) {
            $address->setAddressType($addressType);
            $this->addEntity('address', $address);
        } else {
            $existingAddress->addData($address->getData());
        }
        return $this;
    }
    
    public function setPayment($payment)
    {
        foreach ($this->getEntitiesByType('payment') as $oldPayment) {
            $this->removeEntity($oldPayment);
        }
        $this->addEntity($payment);
        return $this;
    }
    
    public function getPayment()
    {
        $payments = $this->getEntitiesByType('payment');
        if (empty($payments)) {
            return false;
        }
        foreach ($payments as $payment) {
            return $payment;
        }
    }
    
    public function loadByCustomerId($customerId)
    {
        $quotes = Mage::getModel('sales_resource', 'quote')->getQuoteIdsByCustomerId($customerId);
        if (empty($quotes)) {
            return false;
        }
        $this->load($quotes[0]);
        return true;
    }
    
    public function hasItems()
    {
        return !empty($this->_entitiesByType['item']);
    }
    
    public function addProduct(Varien_Data_Object $product)
    {
        if (!$product->getAsNewItem()) {
            foreach ($this->getEntitiesByType('item') as $item) {
                if ($item->getProductId()==$product->getProductId()) {
                    $item->setQty($item->getQty()+$product->getQty());
                    $this->collectTotals();
                    return $this;
                }
            }
        } 
        $item = Mage::getModel('sales', 'quote_entity_item');
        $item->setEntityType('item')->addData($product->getData());
        $this->addEntity($item);
        if ($this->getEstimatePostcode()) {
            $this->estimateShippingMethods();
        }
        $this->collectTotals();
        return $this;
    }
    
    public function updateItems(array $itemsArr)
    {
        foreach ($itemsArr as $id=>$itemUpd) {
            if (!is_numeric($itemUpd['qty']) || $itemUpd['qty']<=0) {
                continue;
            }
            if (!empty($itemUpd['remove'])) {
                $this->removeEntity($id);
            } else {
                $item = $this->getEntitiesById($id);
                if (!$item) {
                    continue;
                }
                $item->setQty($itemUpd['qty']);
            }
        }
        if ($this->getEstimatePostcode()) {
            $this->estimateShippingMethods();
        }
        $this->collectTotals();
        return $this;
    }
    
    public function estimateShippingMethods()
    {
        foreach ($this->getEntitiesByType('shipping') as $entity) {
            if (!$entity->getAddressEntityId()) {
                $this->removeEntity($entity);
            }
        }
        
        $request = Mage::getModel('sales', 'shipping_method_request');
        $request->setDestCountryId(223);
        $request->setDestRegionId(1);
        $request->setDestPostcode($this->getEstimatePostcode());
        $request->setPackageValue($this->getGrandTotal());
        $request->setPackageWeight($this->getWeight());
        
        $shipping = Mage::getModel('sales', 'shipping');
        $methods = $shipping->collectMethods($request)->getAllMethods();
        
        foreach ($methods as $method) {
            $shipping = Mage::getModel('sales', 'quote_entity_shipping');
            $shipping->setCode($method->getVendor().'_'.$method->getService());
            $shipping->setVendor($method->getVendor());
            $shipping->setService($method->getService());
            $shipping->setServiceDescription($method->getServiceTitle());
            $shipping->setAmount($method->getPrice());
            $this->addEntity($shipping);
            
            if ($this->getShippingMethod()==$shipping->getCode()) {
                $this->setShippingAmount($shipping->getAmount());
            }
        }
        
        return $this;
    }
    
    public function collectShippingMethods()
    {
        $shipping = Mage::getModel('sales', 'shipping');
        $addresses = $this->getEntitiesByType('address');
        foreach ($addresses as $address) {
            $request = Mage::getModel('sales', 'shipping_method_request');
            $request->setDestCountryId($address->getCountryId());
            $request->setDestRegionId($address->getRegionId());
            $request->setDestPostcode($address->getPostcode());
            $request->setPackageValue($address->getSubtotal());
            $request->setPackageWeight($address->getRowWeight());

            $methods = $shipping->collectMethods($request);
        }
    }
}