<?php

class Mage_Shipping_Model_Carrier_Tablerate extends Mage_Shipping_Model_Carrier_Abstract
{
    protected $_conditionNames = array();

    public function __construct()
    {
        foreach ($this->getCode('condition_name') as $k=>$v) {
            $this->_conditionNames[] = $k;
        }
    }

	/**
	 * Enter description here...
	 *
	 * @param Mage_Shipping_Model_Rate_Request $data
	 * @return Mage_Shipping_Model_Rate_Result
	 */
	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!Mage::getStoreConfig('carriers/tablerate/active')) {
            return false;
        }

        if (!$request->getConditionName()) {
            $request->setConditionName(Mage::getStoreConfig('carriers/tablerate/condition_name'));
        }

        $result = Mage::getModel('shipping/rate_result');
        $rate = $this->getRate($request);
        if (!empty($rate)) {
	    	$method = Mage::getModel('shipping/rate_result_method');

	    	$method->setCarrier('tablerate');
	    	$method->setCarrierTitle(Mage::getStoreConfig('carriers/tablerate/title'));

	    	$method->setMethod('bestway');
	    	$method->setMethodTitle(Mage::getStoreConfig('carriers/tablerate/name'));

	    	$method->setPrice($rate['price']);
	    	$method->setCost($rate['cost']);

    	    $result->append($method);
        }

    	return $result;
    }

    public function getRate(Mage_Shipping_Model_Rate_Request $request)
    {
        return Mage::getResourceModel('shipping/carrier_tablerate')->getRate($request);
    }

    public function getCode($type, $code='')
    {
        static $codes = array(

            'condition_name'=>array(
                'package_weight' => 'Weight vs. Destination',
                'package_value'  => 'Price vs. Destination',
                'package_qty'    => '# of Items vs. Destination',
            ),

        );

        if (!isset($codes[$type])) {
            throw Mage::exception('Mage_Shipping', 'Invalid TableRate code type: '.$type);
        }

        if (''===$code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            throw Mage::exception('Mage_Shipping', 'Invalid TableRate code for type '.$type.': '.$code);
        }

        return $codes[$type][$code];
    }
}
