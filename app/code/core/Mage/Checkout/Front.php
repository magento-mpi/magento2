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

    public function setStateData($stateName, $data, $value='')
    {
        if (is_string($data) && ('' != $value) ) {
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
        if ('' == $section) {
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
}