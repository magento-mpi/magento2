<?php
/**
 * Customer address controller
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_AddressController extends Mage_Core_Controller_Admin_Action
{
    public function gridDataAction()
    {
        $arrRes = array(
            0 => array(
                'address_id' => 1,
                'address' => 'Formated address string'
            ),
            
            1 => array(
                'address_id' => 2,
                'address' => 'Formated address string'
            )
        );
        
        $this->getResponse()->setBody(Zend_Json::encode($arrRes));
    }
    
    public function formAction()
    {
        
    }
}