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
    
    public function setAddress(Varien_Object $address)
    {
        if (!$address->getAddressType()) {
            throw Mage::exception('Mage_Sales', 'Trying to add address to quote without address_type');
        }
        $old = $this->getAddressByType($address->getAddressType());
        if (!empty($old)) {
            $this->removeEntity($old);
        }
        $entity = Mage::getModel('sales', 'quote_entity_address')->addData($address->getData());
        $this->addEntity($entity);
        return $this;
    }
    
    public function setBillingAddress(Varien_Object $address)
    {
        return $this->setAddress($address->setAddressType('billing'));
    }
    
    public function setShippingAddress(Varien_Object $address)
    {
        return $this->setAddress($address->setAddressType('shipping'));
    }

    public function setPayment(Varien_Object $payment)
    {
        foreach ($this->getEntitiesByType('payment') as $oldPayment) {
            $this->removeEntity($oldPayment);
        }
        $entity = Mage::getModel('sales', 'quote_entity_payment')
            ->addData($payment->getData());
        $this->addEntity($entity);
        return $this;
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
    
    public function addProduct(Varien_Object $product)
    {
        if (!$product->getQty()) {
            $product->setQty(1);
        }
        
        $itemFound = null;
        if (!$product->getAsNewItem()) {
            foreach ($this->getEntitiesByType('item') as $item) {
                if ($item->getProductId()==$product->getProductId()) {
                    $item->setQty($item->getQty()+$product->getQty());
                    $itemFound = $item;
                    break;
                }
            }
        }
        
        if (!$itemFound) {
            $itemFound = Mage::getModel('sales', 'quote_entity_item')
                ->addData($product->getData())
                ->setEntityType('item');
            $this->addEntity($itemFound);
        }
        
        $tierPrice = $product->getTierPrice($itemFound->getQty());
        if ($tierPrice) {
            $itemFound->setPrice($tierPrice);
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
                if (!empty($itemUpd['wishlist'])) {
                    Mage::getModel('customer', 'wishlist')->setProductId($item->getProductId())->save();
                    $this->removeEntity($id);
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
        $attrLogicClasses = Mage::getConfig()->getNode('global/sales/'.$this->getDocType().'/entities/self/attributes')->children();
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
        $attrLogicClasses = Mage::getConfig()->getNode('global/sales/'.$this->getDocType().'/entities/self/attributes')->children();
        
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
            $minPrice = 10000;
            $cheapest = null;
            foreach ($this->getEntitiesByType('shipping') as $method) {
                if ($method->getService() && $method->getAmount()<$minPrice) {
                    $cheapest = $method;
                    $minPrice = $method->getAmount();
                }
            }
            if ($cheapest) {
                $this->setShippingAmount($minPrice, $isChanged);
                $this->setShippingDescription($cheapest->getVendor().' '.$cheapest->getServiceDescription(), $isChanged);
                $code = $cheapest->getCode();
            } else {
                $this->setShippingAmount(0, $isChanged);
                $this->setShippingDescription('', $isChanged);
                $code = '';
            }
        }
        
        $this->setData('shipping_method', $code, $isChanged);
        
        return $this;
    }
    
    public function collectAllShippingMethods()
    {
        $addresses = $this->getEntitiesByType('address');
        foreach ($addresses as $address) {
            if ($address->getAddressType()!='shipping') {
                continue;
            }
            
            $request = Mage::getModel('sales', 'shipping_method_request');
            $request->setDestCountryId($address->getCountryId());
            $request->setDestRegionId($address->getRegionId());
            $request->setDestPostcode($address->getPostcode());
            $request->setPackageValue($address->getSubtotal());
            $request->setPackageWeight($this->getWeight());
            #$request->setAddressEntityId($address->getEntityId());
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
            if ($method instanceof Mage_Sales_Model_Shipping_Method_Service_Error) {
                $shipping->setVendor($method->getVendor());
                $shipping->setErrorMessage($method->getErrorMessage());
            } else {
                $shipping->setCode($method->getVendor().'_'.$method->getService());
                $shipping->setAddressEntityId($request->getAddressEntityId());
                $shipping->setVendor($method->getVendor());
                $shipping->setService($method->getService());
                $shipping->setServiceDescription($method->getServiceTitle());
                $shipping->setAmount($method->getPrice());
            }
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
            if (!$this->getEntitiesById($entity->getAddressEntityId())) {
                $this->removeEntity($entity);
            }
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
        
        $status = $this->getPayment()->getOrderStatus();
        $order->setStatus($status);
        $statusEntity = Mage::getModel('sales', 'order_entity_status')
            ->setStatus($status)
            ->setCreatedAt($now);
            
        $order->validate();
        if ($order->getErrors()) {
            //TODO: handle errors (exception?)
        }
        
        $order->save();
        
        $this->setConvertedAt($now)->setCreatedOrderId($order->getId())->save();
        $this->setLastCreatedOrder($order);
        
        return $this;
    }
}