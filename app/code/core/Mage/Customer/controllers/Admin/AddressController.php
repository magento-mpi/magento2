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
        $arrRes = array();
        $customerId = $this->getRequest()->getParam('id', false);
        if ($customerId) {
            $addressCollection = Mage::getModel('customer_resource', 'address_collection')->loadByCustomerId($customerId);
            foreach ($addressCollection as $address) {
                $arrRes[] = array(
                    'address_id'=> $address->getAddressId(),
                    'address'   => nl2br($address->toString("<strong>{{firstname}} {{lastname}}</strong>\n{{street}}\n{{city}}, {{regionName}} {{postcode}}\nT: {{telephone}}"))
                );
            }
        }
        
        $this->getResponse()->setBody(Zend_Json::encode(array('addresses'=>$arrRes)));
    }

    /**
     * Address form
     */
    public function formAction()
    {
        $addressId = $this->getRequest()->getParam('id', false);
        $customerId = $this->getRequest()->getParam('customer', false);
        
        if (!$customerId) {
            $this->getResponse()->setBody('Empty customer id');
            return;
        }
        
        $address = Mage::getModel('customer', 'address')->load($addressId);

        $form = $this->getLayout()->createBlock('form', 'customer.form');
        $form->setTemplate('form.phtml');

        $form->setAttribute('legend', 'Address information');
        $form->setAttribute('class', 'x-form');
        $form->setAttribute('action', Mage::getBaseUrl().'mage_customer/address/formPost/');
        
        if ($addressId) {
            $form->addField(
                'address_id',
                'hidden',
                array (
                    'name'=>'address_id',
                    'value' => $addressId
                )
            );
        }

        $form->addField(
            'customer_id',
            'hidden',
            array (
                'name'=>'customer_id',
                'value' => $customerId
            )
        );
        
        $form->addField( 'firstname', 'text',
            array(
                'name'  => 'firstname',
                'label' => __('Firstname'),
                'id'    => 'address_firstname',
                'title' => __('Firstname'),
                'validation'=> '',
                'ext_type'  => 'TextField'
            )
        );

        $form->addField('lastname', 'text',
            array(
                'name'  => 'lastname',
                'label' => __('Lastname'),
                'id'    => 'address_lastname',
                'title' => __('Lastname'),
                'validation'=> '',
                'ext_type'  => 'TextField'
            )
        );

        $form->addField('company', 'text',
            array(
                'name'  => 'company',
                'label' => __('Company'),
                'id'    => 'address_company',
                'title' => __('Company'),
                'validation'=> '',
                'ext_type'  => 'TextField'
            )
        );

        $form->addField('country_id', 'select',
            array(
                'name'  => 'country_id',
                'label' => __('Country'),
                'id'    => 'address_country',
                'title' => __('Country'),
                'values'=> Mage::getModel('directory', 'country_collection')->load()->toOptionArray(),
                'validation'=> '',
                'ext_type'  => 'TextField'
            )
        );
        
        $form->addField('region', 'text',
            array(
                'name'  => 'region',
                'label' => __('State/Province'),
                'id'    => 'address_region',
                'title' => __('State/Province'),
                'validation'=> '',
                'ext_type'  => 'TextField'
            )
        );

        $form->addField('city', 'text',
            array(
                'name'  => 'city',
                'label' => __('City'),
                'id'    => 'address_city',
                'title' => __('City'),
                'validation'=> '',
                'ext_type'  => 'TextField'
            )
        );

        $form->addField('street', 'textarea',
            array(
                'name'  => 'street',
                'label' => __('Street Address'),
                'id'    => 'address_street',
                'title' => __('Street Address'),
                'validation'=> '',
                'ext_type'  => 'TextArea'
            )
        );

        $form->addField('postcode', 'text',
            array(
                'name'  => 'postcode',
                'label' => __('Zip/Post Code'),
                'id'    => 'address_postcode',
                'title' => __('Zip/Post code'),
                'validation'=> '',
                'ext_type'  => 'TextField'
            )
        );

        $form->addField('telephone', 'text',
            array(
                'name'  => 'telephone',
                'label' => __('Telephone'),
                'id'    => 'address_telephone',
                'title' => __('Telephone'),
                'validation'=> '',
                'ext_type'  => 'TextField'
            )
        );

        $form->addField('fax', 'text',
            array(
                'name'  => 'fax',
                'label' => __('Fax'),
                'id'    => 'address_fax',
                'title' => __('Fax'),
                'validation'=> '',
                'ext_type'  => 'TextField'
            )
        );
        
        $form->setElementsValues($address->__toArray());
        
        $addressTypes = $address->getAvailableTypes('address_type_id');
        foreach ($addressTypes as $typeId => $info) {
            if (!$address->isPrimary($typeId)) {
                $form->addField('primary_type'.$typeId, 'checkbox',
                    array(
                        'name'  => 'primary_types[]',
                        'label' => __("Use for <strong>%s</strong>", $info['name']),
                        'id'    => 'primary_types_'.$typeId,
                        'title' => __("Use as my primary <strong>%s</strong> address", $info['name']),
                        'value' => $typeId,
                        'validation'=> '',
                        'ext_type'  => 'Checkbox'
                    )
                );
            }
        }
        
        $this->getResponse()->setBody($form->toHtml());
    }

    /**
     * Form post
     */
    public function formPostAction()
    {
        if ($this->getRequest()->isPost()) {
            $address = Mage::getModel('customer', 'address')->setData($this->getRequest()->getPost());
            try {
                $address->save();
            }
            catch (Exception $e){
                echo $e;
            }
        }
    }

    /**
     * Delete address
     */
    public function deleteAction()
    {
        $addressId = $this->getRequest()->getParam('id', false);
        if ($addressId) {
            $address = Mage::getModel('customer', 'address')->setAddressId($addressId);
            try {
                $address->delete();
            }
            catch (Exception $e){
                
            }
        }
    }
}