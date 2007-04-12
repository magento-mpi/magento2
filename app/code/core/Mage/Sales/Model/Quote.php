<?php

class Mage_Sales_Model_Quote extends Varien_Data_Object
{
    protected $_entities = null;
    protected $_attributes = null;
    protected $_entitiesByType = array();
    protected $_entitiesById = array();
    protected $_newEntityId = 0;
    protected $_newAttributeId = 0;
    
    public function __construct($data=array())
    {
        $this->reset();
    }
    
    public function reset()
    {
        $this->_entities = Mage::getModel('sales', 'quote_entity_collection');
        $this->_attributes = Mage::getModel('sales', 'quote_attribute_collection');
        $this->_entitiesByType = array();
        $this->_entitiesById = array();
        $this->_newEntityId = 0;
        $this->_newAttributeId = 0;

        $this->resetChanged(false);
    }
    
    public function getNewEntityId()
    {
        return --$this->_newEntityId;
    }
    
    public function lastNewEntityId()
    {
        return $this->_newEntityId;
    }

    public function getNewAttributeId()
    {
        return --$this->_newAttributeId;
    }
    
    public function lastNewAttributeId()
    {
        return $this->_newAttributeId;
    }
    
    public function getAttributes()
    {
        return $this->_attributes;
    }
    
    public function getEntities()
    {
        return $this->_entities;
    }
    
    public function getEntitiesByType($type)
    {
        if (isset($this->_entitiesByType[$type])) {
            return $this->_entitiesByType[$type];
        }
        return array();        
    }
    
    public function getEntityById($id)
    {
        if (isset($this->_entitiesById[$id])) {
            return $this->_entitiesById[$id];
        }
        return false;  
    }
    
    public function getEntitiesByAttribute($type, $attribute, $value=null)
    {
        $entities = $this->getEntitiesByType($type);
        $entArr = array();
        foreach ($entities as $entity) {
            if ($value==$entity->getAttribute($attribute)) {
                $entArr[] = $entity;
            }
        }
        return $entArr;
    }
    
    public function getQuoteEntity()
    {
        if (empty($this->_entitiesByType['quote'])) {
            $this->addEntity('quote', Mage::getModel('sales', 'quote_entity'));
        }
        foreach ($this->getEntitiesByType('quote') as $quote) {
            return $quote;
        }
    }
    
    public function setEntity($entity)
    {
        $id = $entity->getQuoteEntityId();
        $type = $entity->getEntityType();
        $this->_entitiesById[$id] = $entity;
        $this->_entitiesByType[$type][$id] = $entity;
    }
    
    public function addEntity($type, Varien_Data_Object $source=null)
    {
        $entity = Mage::getModel('sales', 'quote_entity');
        $entity->setQuote($this);
        
        if (!is_null($source)) {
            $entity->importDataObject($source, $type);
        }
        
        if (!$entity->hasQuoteEntityId()) {
            $entity->setQuoteEntityId($this->getNewEntityId());
        }
        
        $this->_entities->addItem($entity);
        $this->setEntity($entity);
        
        return $this;
    }
    
    public function removeEntity($entityToRemove)
    {
        if ($entityToRemove instanceof Mage_Sales_Model_Quote_Entity) {
            $entityId = $entity->getQuoteEntityId();
        } else {
            $entityId = (int)$entityToRemove;
        }
        
        $entityToRemove = $this->getEntityById($entityId);
        if (empty($entityToRemove)) {
            return $this;
        }
        
        unset($this->_entitiesById[$entityId]);
        unset($this->_entitiesByType[$entityToRemove->getEntityType()][$entityId]);
        
        $entities = $this->getEntities();
        foreach ($entities as $key=>$entity) {
            if ($entity->getQuoteEntityId()===$entityToRemove->getQuoteEntityId()) {
                $entities->removeItemByKey($key);
            }
        }
    }
    
    public function deleteEntity($entityToRemove)
    {
        if ($entityToRemove instanceof Mage_Sales_Model_Quote_Entity) {
            $entityId = $entity->getQuoteEntityId();
        } else {
            $entityId = (int)$entityToRemove;
        }
        
        $entityToRemove = $this->getEntityById($entityId);
        if (empty($entityToRemove)) {
            return $this;
        }
        
        unset($this->_entitiesById[$entityId]);
        unset($this->_entitiesByType[$entityToRemove->getEntityType()][$entityId]);

        $entityToRemove->setDeleteFlag(true);       
    }
    
    protected function _afterLoad()
    {
        foreach ($this->_entities->getItems() as $entity) {
            $type = $entity->getEntityType();
            $id = $entity->getQuoteEntityId();
            $this->_entitiesByType[$type][$id] = $entity;
            $this->_entitiesById[$id] = $entity;
            $entity->setQuote($this);
        }
        
        foreach ($this->_attributes->getItems() as $attr) {
            $id = $attr->getQuoteEntityId();
            $code = $attr->getAttributeCode();
            $this->_entitiesById[$id]->setAttribute($attr);
            $attr->setEntity($this->getEntityById($id));
        }
        return $this;
    }
    
    public function getAddressByType($type)
    {
        foreach ($this->getEntitiesByType('address') as $addr) {
            if ($addr->getAttribute('quote_address_type/varchar')==$type) {
                return $addr;
            }
        }
        return false;
    }
    
    public function setAddress($addressType, Mage_Customer_Model_Address $address)
    {
        $existingAddress = $this->getAddressByType($addressType);
        if (empty($existingAddress)) {
            $address->setQuoteAddressType($addressType);
            $this->addEntity('address', $address);
        } else {
            $existingAddress->importDataObject($address, 'address');
        }
        return $this;
    }
    
    public function setPayment(Mage_Customer_Model_Payment $payment)
    {
        $existingPayment = $this->getPayment();
        if (empty($existingPayment)) {
            $this->addEntity('payment', $payment);
        } else {
            $existingPayment->importDataObject($payment, 'payment');
        }
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
    
    public function addProduct(Mage_Catalog_Model_Product $product)
    {
        if (!$product->getAsNewItem()) {
            $dups = $this->getEntitiesByAttribute('item', 'product_id', $product->getProductId());
            if (!empty($dups)) {
                $dups[0]->setAttribute('qty', $dups[0]->getAttribute('qty')+$product->getQty());
                return $this;
            }
        }
        $this->addEntity('item', $product);
        $this->collectTotals();
        return $this;
    }
    
    public function collectTotals($type='')
    {
        $attrLogicClasses = Mage::getConfig()->getGlobalCollection('salesQuoteAttributes')->children();
        $totalsArr = array();
        foreach ($attrLogicClasses as $attrName=>$attrConfig) {
            $className = $attrConfig->getClassName();
            $attrLogic = new $className();
            $arr = $attrLogic->collectTotals($this);
            foreach ($arr as $i=>$row) {
                if (''!==$type && $row['code']!==$type || '_output'===$type && empty($row['output'])) {
                    unset($arr[$i]);
                }
            }
            $totalsArr = array_merge_recursive($totalsArr, $arr);
        }
        return $totalsArr;
    }
    
    public function collectShippingMethods()
    {
        $shippingEntity = $this->getAddressByType('shipping');
        if (empty($shippingEntity)) {
            return array();
        }
        $shippingAddress = $shippingEntity->asModel('customer', 'address');

        $request = Mage::getModel('sales_model', 'shipping_method_request');
        $request->setDestCountryId($shippingAddress->getCountryId());
        $request->setDestRegionId($shippingAddress->getRegionId());
        $request->setDestPostcode($shippingAddress->getPostcode());
        $request->setOrderSubtotal($this->getQuoteEntity()->getAttribute('subtotal'));
        $request->setPackageWeight($this->getQuoteEntity()->getAttribute('weight'));
        $shipping = Mage::getModel('sales_model', 'shipping');
        $result = $shipping->collectMethods($request);
        $allMethods = $result->getAllMethods();
        
        $currencyFilter = new Varien_Filter_Sprintf('$%s', 2);
        $methods = array();
        if (!empty($allMethods)) {
            foreach ($allMethods as $method) {
                $methods[$method->getVendor()]['title'] = $method->getVendorTitle();
                $methods[$method->getVendor()]['methods'][$method->getService()] = array(
                    'title'=>$method->getServiceTitle(),
                    'price'=>$method->getPrice(),
                    'price_formatted'=>$currencyFilter->filter($method->getPrice()),
                );
            }
        }

        return $methods;
    }
    
    public function hasItems()
    {
        $items = $this->getEntitiesByType('item');
        return !empty($items);
    }
    
    public function getItemsAsArray($fields=null)
    {
        $arr = array();
        if (is_null($fields)) {
            $fields = Mage::getModel('sales', 'quote_entity')->getDefaultType('', 'item');
        }
        
        $items = $this->getEntitiesByType('item');
        if (is_array($items) && !empty($items)) {
            foreach ($items as $id=>$item) {
                $arr[$id]['id'] = $id;
                $types = $item->getDefaultAttributeType();
                foreach ($item->getAttributes() as $attr) {
                    $code = $attr->getAttributeCode();
                    $arr[$id][$code] = $attr->getData('attribute_'.$types[$code]);
                }
            }
        }
               
        return $arr;
    }
    
    public function updateItems(array $itemsArr)
    {
        foreach ($itemsArr as $id=>$itemUpd) {
            if (!empty($itemUpd['remove'])) {
                $this->getEntityById($id)->setDeleteFlag(true);
            } else {
                $item = $this->getEntityById($id);
                if (!$item) {
                    continue;
                }
                $item->setAttribute('qty', $itemUpd['qty']);
            }
        }
        $this->collectTotals();
        return $this;
    }

}