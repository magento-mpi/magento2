<?php
/**
 * Cart front
 *
 * @package    Ecom
 * @subpackage Cart
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Cart_Front
{

    protected $_state;

    public function __construct()
    {
        $this->_state = new Zend_Session_Namespace('Mage_Cart');
    }

    public static function construct()
    {
        if (!Mage::registry('Mage_Cart')) {
            Mage::register('Mage_Cart', new Mage_Cart_Front());
        }
    }

    public static function registerCustomer()
    {
        
    }
}