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
    

    public function getResult()
    {
        if (empty($this->_result)) {
            $this->_result = Mage::getModel('shipping/rate_result');
        }
        return $this->_result;
    }
    
    /**
     * Set shipping orig data
     */
    public function setOrigData($data)
    {
        $this->_orig = $data;
    }

    /**
     * Reset cached result
     */
    public function resetResult()
    {
        $this->getResult()->reset();
        return $this;
    }

    /**
     * Retrieve all methods for supplied shipping data
     * 
     * @param Mage_Shipping_Model_Shipping_Method_Request $data
     * @return Mage_Shipping_Model_Shipping
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
            $carriers = Mage::getStoreConfig('carriers');

            foreach ($carriers as $carrierCode=>$carrierConfig) {
                if (!$carrierConfig->is('active', 1)) {
                    continue;
                }
                $className = $carrierConfig->getClassName();
                if (!$className) {
                    continue;
                }
                $obj = Mage::getModel($className);
                if (!$obj) {
                    continue;
                }
                
                $request->setCarrier($carrierCode);
                $result = $obj->collectRates($request);
                
                $this->getResult()->append($result);
            }
        } else {
            $carrierConfig = Mage::getStoreConfig('carriers/'.$request->getLimitCarrier());
            if (!$carrierConfig) {
                return $this;
            }
            $className = $carrierConfig->getClassName();
            $obj = Mage::getModel($className);
            $result = $obj->collectRates($request);
            $this->getResult()->append($result);
        }
        
        return $this;
    }
    
    public function collectRatesByAddress(Varien_Object $address)
    {
        $request = Mage::getModel('shipping/rate_request');
        $request->setDestCountryId($address->getCountryId());
        $request->setDestRegionId($address->getRegionId());
        $request->setDestPostcode($address->getPostcode());
        $request->setPackageValue($address->getSubtotal());
        $request->setPackageWeight($address->getWeight());
        $request->setPackageQty($address->getItemQty());

        return $this->collectRates($request);
    }
}
