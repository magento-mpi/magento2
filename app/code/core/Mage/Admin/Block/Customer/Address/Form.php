<?php
/**
 * Admin customer address form
 *
 * @package     Mage
 * @subpackage  Admin
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Admin_Block_Customer_Address_Form extends Varien_Data_Form 
{
    public function __construct() 
    {
        parent::__construct();
        $this->setId('customer_address_form');
        $this->setAction(Mage::getUrl('admin', array('controller'=>'customer', 'action'=>'addressSave')));
        
        $customerId = false;
        if ($customer) {
            $customerId = $customer->getId();
        }
        
        $this->addField( 'firstname', 'text',
            array(
                'name'  => 'firstname',
                'label' => __('Firstname'),
                'id'    => 'address_firstname',
                'title' => __('Firstname'),
                'validation'=> '',
                'ext_type'  => 'TextField'
            )
        );

        $this->addField('lastname', 'text',
            array(
                'name'  => 'lastname',
                'label' => __('Lastname'),
                'id'    => 'address_lastname',
                'title' => __('Lastname'),
                'validation'=> '',
                'ext_type'  => 'TextField'
            )
        );

        $this->addField('company', 'text',
            array(
                'name'  => 'company',
                'label' => __('Company'),
                'id'    => 'address_company',
                'title' => __('Company'),
                'validation'=> '',
                'ext_type'  => 'TextField'
            )
        );

        $this->addField('country_id', 'select',
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
        
        $this->addField('region', 'text',
            array(
                'name'  => 'region',
                'label' => __('State/Province'),
                'id'    => 'address_region',
                'title' => __('State/Province'),
                'validation'=> '',
                'ext_type'  => 'TextField'
            )
        );

        $this->addField('city', 'text',
            array(
                'name'  => 'city',
                'label' => __('City'),
                'id'    => 'address_city',
                'title' => __('City'),
                'validation'=> '',
                'ext_type'  => 'TextField'
            )
        );

        $this->addField('street', 'textarea',
            array(
                'name'  => 'street',
                'label' => __('Street Address'),
                'id'    => 'address_street',
                'title' => __('Street Address'),
                'validation'=> '',
                'ext_type'  => 'TextArea'
            )
        );

        $this->addField('postcode', 'text',
            array(
                'name'  => 'postcode',
                'label' => __('Zip/Post Code'),
                'id'    => 'address_postcode',
                'title' => __('Zip/Post code'),
                'validation'=> '',
                'ext_type'  => 'TextField'
            )
        );

        $this->addField('telephone', 'text',
            array(
                'name'  => 'telephone',
                'label' => __('Telephone'),
                'id'    => 'address_telephone',
                'title' => __('Telephone'),
                'validation'=> '',
                'ext_type'  => 'TextField'
            )
        );

        $this->addField('fax', 'text',
            array(
                'name'  => 'fax',
                'label' => __('Fax'),
                'id'    => 'address_fax',
                'title' => __('Fax'),
                'validation'=> '',
                'ext_type'  => 'TextField'
            )
        );
        
        //$this->setElementsValues($address->toArray());
        
        /*$addressTypes = $address->getAvailableTypes('address_type_id');
        foreach ($addressTypes as $typeId => $info) {
            if (!$address->isPrimary($typeId)) {
                $this->addField('primary_type'.$typeId, 'checkbox',
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
        }*/
    }
}
