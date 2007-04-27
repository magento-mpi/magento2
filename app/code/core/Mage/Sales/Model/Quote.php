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
                    $item->setPrice($product->getTierPrice($item->getQty()));
                    $this->collectTotals();
                    return $this;
                }
            }
        }
        $item = Mage::getModel('sales', 'quote_entity_item');
        $item->setEntityType('item')->addData($product->getData());
        $this->addEntity($item);
        $item->setPrice($product->getTierPrice($item->getQty()));
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
                $product = Mage::getModel('catalog', 'product')->load($item->getProductId());
                $item->setQty($itemUpd['qty']);
                $item->setPrice($product->getTierPrice($item->getQty()));
            }
        }
        $this->collectTotals();
        return $this;
    }

    public function collectTotals($type='')
    {
        $attrLogicClasses = Mage::getConfig()->getXml('global/salesAttributes/'.$this->getDocType())->self->children();
        foreach ($attrLogicClasses as $attrName=>$attrConfig) {
            $className = $attrConfig->getClassName();
            if (empty($className)) {
                continue;
            }
            $attrLogic = new $className();
            $arr = $attrLogic->collectTotals($this);
        }
        return $this;
    }
    
    public function getTotals($type='_output')
    {
        $attrLogicClasses = Mage::getConfig()->getXml('global/salesAttributes/'.$this->getDocType())->self->children();
        
        $totalsArr = array();
        foreach ($attrLogicClasses as $attrName=>$attrConfig) {
            $className = $attrConfig->getClassName();
            if (empty($className)) {
                continue;
            }
            $attrLogic = new $className();
            $arr = $attrLogic->getTotals($this);
            foreach ($arr as $i=>$row) {
                if ('_output'!==$type && ''!==$type && $row['code']!==$type 
                    || '_output'===$type && empty($row['output'])) {
                    unset($arr[$i]);
                }
            }

            $totalsArr = array_merge_recursive($totalsArr, $arr);
        }
        return $totalsArr;
    }
    
    public function estimateShippingMethods()
    {
        $request = Mage::getModel('sales', 'shipping_method_request');
        $request->setDestCountryId(223);
        $request->setDestRegionId(1);
        $request->setDestPostcode($this->getEstimatePostcode());
        $request->setPackageValue($this->getSubtotal());
        $request->setPackageWeight($this->getWeight());

        $this->collectAddressShippingMethods($request);
        $this->setShippingMethod($this->getShippingMethod());

        return $this;
    }
    
    public function setShippingMethod($code, $isChanged=true)
    {
        $found = false;
        if ($code) {
            foreach ($this->getEntitiesByType('shipping') as $method) {
                if ($method->getCode()===$code) {
                    $this->setShippingAmount($method->getAmount(), $isChanged);
                    $this->setShippingDescription($method->getVendor().' '.$method->getServiceDescription(), $isChanged);
                    $found = true;
                    break;
                }
            }
        }
        if (!$found) {
            $this->setShippingAmount(0, $isChanged);
            $this->setShippingDescription('', $isChanged);
            $code = '';
        }
        
        $this->setData('shipping_method', $code, $isChanged);
        
        return $this;
    }
    
    public function collectAllShippingMethods()
    {
        $addresses = $this->getEntitiesByType('address');
        foreach ($addresses as $address) {
            $request = Mage::getModel('sales', 'shipping_method_request');
            $request->setDestCountryId($address->getCountryId());
            $request->setDestRegionId($address->getRegionId());
            $request->setDestPostcode($address->getPostcode());
            $request->setPackageValue($address->getSubtotal());
            $request->setPackageWeight($address->getRowWeight());
            $request->setAddressEntityId($address->getEntityId());
            $this->collectAddressShippingMethods($request);
        }
    }
    
    public function collectAddressShippingMethods(Mage_Sales_Model_Shipping_Method_Request $request)
    {
        $this->removeAddressShippingMethods($request->getAddressEntityId());
        
        $result = Mage::getModel('sales', 'shipping')->collectMethods($request);
        if (!$result) {
            return $this;
        }
        $methods = $result->getAllMethods();
        
        foreach ($methods as $method) {
            $shipping = Mage::getModel('sales', 'quote_entity_shipping');
            $shipping->setCode($method->getVendor().'_'.$method->getService());
            $shipping->setAddressEntityId($request->getAddressEntityId());
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
    
    public function removeAddressShippingMethods($addressEntityId)
    {
        foreach ($this->getEntitiesByType('shipping') as $entity) {
            if ($entity->getAddressEntityId()==$addressEntityId) {
                $this->removeEntity($entity);
            }
        }
        return $this;
    }
    
    public function createOrders()
    {
        $website = Mage::registry('website');
        $now = new Zend_Db_Expr("now()");
        
        $order = Mage::getModel('sales', 'order')->addData($this->getData());
        
        $order->setRealOrderId(Mage::getModel('sales_resource', 'counter')->getCounter('order'))            
            ->setRemoteIp(Mage::registry('controller')->getRequest()->getServer('REMOTE_ADDR'))
            ->setCreatedAt($now)
            ->setWebsiteId($website->getId())
            ->setCurrencyId($website->getCurrencyId())
            ->setCurrencyBaseId($website->getCurrencyBaseId())
            ->setCurrencyRate($website->getCurrencyRate());
        
        foreach (array('item', 'address', 'payment') as $entityType) {
            $entities = $this->getEntitiesByType($entityType);
            foreach ($entities as $quoteEntity) {
                $entity = Mage::getModel('sales', 'order_entity_'.$entityType)->addData($quoteEntity->getData());
                $order->addEntity($entity);
            }
        }
        
        $statusId = $this->getPayment()->getOrderStatusId();
        $order->setStatus($statusId);
        $statusEntity = Mage::getModel('sales', 'order_entity_status')
            ->setStatusId($statusId)
            ->setCreatedAt($now);
        
        $order->save();
        
        $this->setConvertedAt($now)->save();
        
        return $this;
    }
}