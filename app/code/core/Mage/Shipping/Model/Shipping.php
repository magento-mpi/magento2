<?php

class Mage_Shipping_Model_Shipping
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
        $this->_result = new Mage_Shipping_Model_Rate_Result();
    }
    
    /**
     * Set shipping orig data
     */
    public function setOrigData($data)
    {
        $this->_orig = $data;
    }

    /**
     * Retrieve all methods for supplied shipping data
     * 
     * @param Mage_Sales_Model_Shipping_Method_Request $data
     * @return Mage_Sales_Model_Shipping_Method_Result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!$request->getOrig()) {
            $request
                ->setCountryId(Mage::getStoreConfig('shipping/origin/country_id'))
                ->setRegionId(Mage::getStoreConfig('shipping/origin/region_id'))
                ->setPostcode(Mage::getStoreConfig('shipping/origin/postcode'))
            ;
        }

        if (!$request->getLimitCarrier()) { 
            $carriers = Mage::getConfig()->getNode('global/sales/shipping/carriers')->children();

            foreach ($carriers as $carrier) {
                if (!$carrier->is('active')) {
                    continue;
                }
                $request->setVendor($carrier->getName());
                $className = $carrier->getClassName();
                $obj = new $className();
                $result = $obj->collectRates($request);
                $this->_result->append($result);
            }
        } else {
            $className = Mage::getConfig()->getNode('global/sales/shipping/carriers/'.$request->getLimitCarrier())->getClassName();
            $obj = new $className();
            $result = $obj->collectRates($request);
            $this->_result->append($result);
        }
        
        return $this->_result;
    }
    
    public function collectRatesByAddress(Varien_Object $address)
    {
        $request = Mage::getModel('shipping/rate_request');
        $request->setDestCountryId($address->getCountryId());
        $request->setDestRegionId($address->getRegionId());
        $request->setDestPostcode($address->getPostcode());
        $request->setPackageValue($address->getSubtotal());
        $request->setPackageWeight($address->getWeight());

        return $this->collectRates($request);
    }
}