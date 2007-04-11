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
        $addressId = $this->getRequest()->getParam('id', false);
        $address = Mage::getModel('customer', 'address');
        $address->load($addressId);

        $form = Mage::createBlock('form', 'customer.form');
        $form->setViewName('Mage_Core', 'form.phtml');

        $form->setAttribute('legend', 'Address information');
        $form->setAttribute('class', 'x-form');
        $form->setAttribute('action', Mage::getBaseUrl().'/mage_customer/address/formPost/');

        $form->addField(
            'address_id',
            'hidden',
            array (
                'name'=>'customer_id',
                'value' => $addressId
            )
        );

        $form->addField(
            'customer_firstname',
            'text',
            array(
                'name'  => 'customer_firstname',
                'label' => __('Firstname'),
                'id'    => 'customer_firstname',
                'title' => __('Customer Firstname'),
                'validation'=> '',
                'ext_type'  => 'TextField'
            )
        );

        $form->addField(
            'customer_lastname',
            'text',
            array(
                'name'  => 'customer_lastname',
                'label' => __('Lastname'),
                'id'    => 'customer_lastname',
                'title' => __('Customer Lastname'),
                'validation'=> '',
                'ext_type'  => 'TextField'
        	)
        );

        $form->addField(
            'customer_email',
            'text',
            array(
                'name'  => 'customer_email',
                'label' => __('Email'),
                'id'    => 'customer_email',
                'title' => __('Customer Email'),
                'validation'=> '',
                'ext_type'  => 'TextField'
            )
        );

        $form->setElementsValues($address->__toArray());
        $this->getResponse()->setBody($form->toString());
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