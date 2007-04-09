<?php
/**
 * Checkout front
 *
 * @package    Ecom
 * @subpackage Checkout
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Checkout_Front
{
    
    protected $_state;
    
    public function __construct()
    {
        $this->_state = new Zend_Session_Namespace('Mage_Checkout');
    }
    
    public static function construct()
    {
        if (!Mage::registry('Mage_Checkout')) {
            Mage::register('Mage_Checkout', new Mage_Checkout_Front());
        }
    }
    
    public static function clear()
    {
        Mage::registry('Mage_Checkout')->clearState();
    }

    public function setStateData($stateName, $data, $value=null)
    {
        if (is_string($data) && (!is_null($value)) ) {
            $prevData = $this->_state->$stateName;
            if (!is_array($prevData)) {
                $prevData = array();
            }
            $prevData[$data] = $value;
            $this->_state->$stateName = $prevData;
        }
        else {
            $this->_state->$stateName = $data;
        }
        
        return $this;
    }
    
    public function getStateData($stateName, $section = '')
    {
        if (''===$section) {
            return $this->_state->$stateName;
        }
        else {
            $data = $this->_state->$stateName;
            return isset($data[$section]) ? $data[$section] : false;
        }

    }

    public function clearState()
    {
        $this->_state->unsetAll();
    }

    public function fetchShippingMethods()
    {
        $shippingAddress = $this->getStateData('shipping', 'address');
        
        $cart = new Mage_Cart_Cart();
        $cartTotals = $cart->getTotals();
        $subtotal = $cartTotals->asArray('subtotal');
        $weight = $cartTotals->asArray('weight');

        $request = new Mage_Sales_Model_Shipping_Quote_Request();
        $request->setDestCountryId($shippingAddress->getCountryId());
        $request->setDestRegionId($shippingAddress->getRegionId());
        $request->setDestPostcode($shippingAddress->getPostcode());
        $request->setOrderSubtotal($subtotal[0]['value']);
        $request->setPackageWeight($weight[0]['value']);

        $shipping = new Mage_Sales_Model_Shipping();
        $result = $shipping->fetchQuotes($request);
        $allQuotes = $result->getAllQuotes();
        
        $quotes = array();
        if (!empty($allQuotes)) {
            foreach ($allQuotes as $quote) {
                $priceFilter = new Varien_Filter_Sprintf('$%s', 2);
                $quotes[$quote->getVendor()]['title'] = $quote->getVendorTitle();
                $quotes[$quote->getVendor()]['methods'][$quote->getService()] = array(
                    'code'=>$quote->getService(),
                    'title'=>$quote->getServiceTitle(),
                    'price'=>$priceFilter->filter($quote->getPrice()),
                );
            }
        }

        $this->setStateData('shipping_method', 'quotes', $quotes);
    }
}