<?php
/**
 * Registration form block
 *
 * @package    Mage
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Block_Regform extends Mage_Core_Block_Form 
{
    public function __construct() 
    {
        $this->setTemplate('customer/form/registration.phtml');
        
        $this->setAttribute('method', 'post');
        $this->setAttribute('action', Mage::getUrl('customer', array('controller'=>'account', 'action'=>'register')));
        $this->setAttribute('class', 'regform');
        
        $countries = Mage::getResourceModel('directory/country_collection');
        $this->assign('countries', $countries->load());
    }
}