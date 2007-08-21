<?php

class Mage_Customer_Model_Entity_Setup extends Mage_Eav_Model_Entity_Setup
{
    public function getDefaultEntities()
    {
        return array(
            'customer'=>array(
                'table'=>'customer/entity',
                'increment_model'=>'eav/entity_increment_numeric',
                'increment_per_store'=>false,
                'attributes' => array(
                	'firstname' => array('label'=>'First Name'),
                	'lastname' => array('label'=>'Last Name'),
                	'email' => array('label'=>'Email', 'class'=>'validate-email'),
                	'password_hash' => array('backend'=>'customer_entity/customer_attribute_backend_password', 'required'=>false),
                	'customer_group' => array('type'=>'int', 'input'=>'select', 'label'=>'Customer Group', 'source'=>'customer_entity/customer_attribute_source_group'),
                	'store_balance' => array('type'=>'decimal', 'input'=>'text', 'label'=>'Balance', 'class'=>'validate-number'),
                	'default_billing' => array('type'=>'int', 'visible'=>false, 'required'=>false, 'backend'=>'customer_entity/customer_attribute_backend_billing'),
                	'default_shipping' => array('type'=>'int', 'visible'=>false, 'required'=>false, 'backend'=>'customer_entity/customer_attribute_backend_shipping'),
                ),
            ),
            
            'customer_address'=>array(
                'table'=>'customer/entity',
                'attributes' => array(
                	'firstname' => array('label'=>'First Name'),
                	'lastname' => array('label'=>'Last Name'),
                	'country_id' => array('type'=>'int', 'input'=>'select', 'label'=>'Country', 'class'=>'countries input-text', 'source'=>'customer_entity/address_attribute_source_country'),
                	'region' => array('backend'=>'customer_entity/address_attribute_backend_region', 'label'=>'State/Province', 'class'=>'regions'),
                	'region_id' => array('type'=>'int', 'input'=>'hidden', 'source'=>'customer_entity/address_attribute_source_region', 'required'=>'false'),
                	'postcode' => array('label'=>'Zip/Post Code'),
                	'city' => array('label'=>'City'),
                	'street' => array('type'=>'text', 'backend'=>'customer_entity/address_attribute_backend_street', 'input'=>'textarea', 'label'=>'Street Address'),
                	'telephone' => array('label'=>'Telephone'),
                	'fax' => array('label'=>'Fax', 'required'=>false),
                	'company' => array('label'=>'Company', 'required'=>false),
                ),
            ),
            
            'customer_payment'=>array(
                'table'=>'customer/entity',
                'attributes' => array(
                	'method_type'=>array('type'=>'int', 'input'=>'select', 'label'=>'Payment Method'),
                ),
            ),
        );
    }
}