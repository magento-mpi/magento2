<?php
/**
 * Registration form block
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Block_Regform extends Mage_Core_Block_Form 
{
    public function __construct() 
    {
        $this->setViewName('Mage_Customer', 'form/registration.phtml');
        
        $this->setAttribute('method', 'post');
        $this->setAttribute('action', Mage::getBaseUrl('', 'Mage_Customer').'/account/register/');
        $this->setAttribute('class', 'regform');
        
        $countries = Mage::getResourceModel('directory', 'country_collection');
        $this->assign('countries', $countries->load());
            
        $data = '';
        
        $this->addField('firstname', 'text', array('name'=>'firstname'));
        $this->addField('lastname', 'text', array('name'=>'lastname'));
    }
}