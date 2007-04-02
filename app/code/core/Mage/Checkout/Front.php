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
    
    public function setStateData($stateName, $data)
    {
        $this->_state->$stateName = $data;
        return $this;
    }
    
    public function getStateData($stateName)
    {
        return $this->_state->$stateName;
    }
    
    public function clearState()
    {
        $this->_state = array();
    }
}