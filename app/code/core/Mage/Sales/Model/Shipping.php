<?php

class Mage_Sales_Model_Shipping
{
    /**
     * Default shipping orig for requests
     *
     * @var array
     */
    protected $_orig = null;
    
    /**
     * Cached result
     * 
     * @var Mage_Sales_Model_Shipping_Method_Result
     */
    protected $_result = null;
    
    /**
     * Reset cached result
     */
    public function resetResult()
    {
        $this->_result->reset();
        return $this;
    }
    
    /**
     * Constructor
     *
     * Initializes $_result object
     */
    public function __construct()
    {
        $this->_result = new Mage_Sales_Model_Shipping_Method_Result();
    }
    
    /**
     * Set shipping orig data
     */
    public function setOrigData($data)
    {
        $this->_orig = $data;
    }
    
    public function getOrigData()
    {
        if (!isset($this->_orig)) {
            $this->setOrigData(Mage::getSingleton('sales', 'config')->getShippingOrig());
        }
        return $this->_orig;
    }
    
    /**
     * Retrieve all methods for supplied shipping data
     * 
     * @param Mage_Sales_Model_Shipping_Method_Request $data
     * @return Mage_Sales_Model_Shipping_Method_Result
     */
    public function collectMethods(Mage_Sales_Model_Shipping_Method_Request $request)
    {
        if (!$request->getOrig()) {
            $request->addData($this->getOrigData());
        }

        if (!$request->limitVendor) { 
            $vendors = Mage::getConfig()->getNode('global/sales/shipping/vendors')->children();

            foreach ($vendors as $vendor) {
                if (!$vendor->is('active')) {
                    continue;
                }
                $request->setVendor($vendor->getName());
                $className = $vendor->getClassName();
                $obj = new $className();
                $result = $obj->collectMethods($request);
                $this->_result->append($result);
            }
        } else {
            $className = Mage::getConfig()->getNode('global/sales/shipping/vendors/'.$request->limitVendor)->getClassName();
            $obj = new $className();
            $result = $obj->collectMethods($request);
            $this->_result->append($result);
        }
        
        return $this->_result;
    }
    
    public function collectMethodsByAddress(Varien_Object $address)
    {
        $request = Mage::getModel('sales', 'shipping_method_request');
        $request->setDestCountryId($address->getCountryId());
        $request->setDestRegionId($address->getRegionId());
        $request->setDestPostcode($address->getPostcode());
        $request->setPackageValue($address->getSubtotal());
        $request->setPackageWeight($address->getWeight());

        return $this->collectMethods($request);
    }
}