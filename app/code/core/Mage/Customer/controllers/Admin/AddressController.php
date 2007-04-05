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
    /**
     * Addresses JSON
     */
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

            //$this->getResponse()->setBody(Zend_Json::encode($arrRes));
            $this->getResponse()->setBody('{addresses:[{addr_id:0, address: "street lines", city: "Los Angeles", state:"California", zip: "09210", country: "USA"}, 
													   {addr_id:1, address: "street lines2", city: "New York", state:"New York", zip: "02950", country: "USA"},
													   {addr_id:2, address: "street lines2", city: "New York", state:"New York", zip: "02950", country: "USA"},
													   {addr_id:3, address: "street lines2", city: "New York", state:"New York", zip: "02950", country: "USA"},
													   {addr_id:4, address: "street lines2", city: "New York", state:"New York", zip: "02950", country: "USA"},
													   {addr_id:5, address: "street lines2", city: "New York", state:"New York", zip: "02950", country: "USA"}
											]}');
    }

    /**
     * Address form
     */
    public function formAction()
    {
        
    }

    /**
     * Form post
     */
    public function formPostAction()
    {
        
    }

    /**
     * Delete address
     */
    public function deleteAction()
    {
        
    }
}