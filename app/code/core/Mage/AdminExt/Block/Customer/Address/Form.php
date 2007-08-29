<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_AdminExt
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Admin customer address form
 *
 * @category   Mage
 * @package    Mage_AdminExt
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Admin_Block_Customer_Address_Form extends Varien_Data_Form 
{
    public function __construct($address=null) 
    {
        parent::__construct();
        $this->setId('customer_address_form');
        $this->setAction(Mage::getUrl('admin/customer/addressSave'));
        
        $fieldset = $this->addFieldset('base_fieldset', array('legend'=>__('Customer Address')));
        $fieldset->addField( 'firstname', 'text',
            array(
                'name'  => 'firstname',
                'label' => __('First Name'),
                'id'    => 'address_firstname',
                'title' => __('First Name'),
                'vtype' => 'alphanum',
                'allowBlank' => false
            )
        );

        $fieldset->addField('lastname', 'text',
            array(
                'name'  => 'lastname',
                'label' => __('Last Name'),
                'id'    => 'address_lastname',
                'title' => __('Last Name'),
                'vtype' => 'alphanum',
                'allowBlank' => false
            )
        );

        $fieldset->addField('company', 'text',
            array(
                'name'  => 'company',
                'label' => __('Company'),
                'id'    => 'address_company',
                'title' => __('Company'),
                'vtype' => 'alphanum',
            )
        );

        $fieldset->addField('country_id', 'select',
            array(
                'name'  => 'country_id',
                'label' => __('Country'),
                'id'    => 'address_country',
                'title' => __('Country'),
                'values'=> Mage::getResourceModel('directory/country_collection')->load()->toOptionArray(),
                'allowBlank'=> false,
                'emptyText' => __('Select a Country ...')
            )
        );
        
        $fieldset->addField('region', 'text',
            array(
                'name'  => 'region',
                'label' => __('State/Province'),
                'id'    => 'address_region',
                'title' => __('State/Province'),
                'validation'=> '',
                'vtype' => 'alphanum',
                'allowBlank' => false
            )
        );

        $fieldset->addField('city', 'text',
            array(
                'name'  => 'city',
                'label' => __('City'),
                'id'    => 'address_city',
                'title' => __('City'),
                'vtype' => 'alphanum',
                'allowBlank' => false
            )
        );

        $fieldset->addField('street', 'textarea',
            array(
                'name'  => 'street',
                'label' => __('Street Address'),
                'id'    => 'address_street',
                'title' => __('Street Address'),
                'allowBlank' => false
            )
        );

        $fieldset->addField('postcode', 'text',
            array(
                'name'  => 'postcode',
                'label' => __('Zip/Post Code'),
                'id'    => 'address_postcode',
                'title' => __('Zip/Post Code'),
                'vtype' => 'numeric',
                'allowBlank' => false
            )
        );

        $fieldset->addField('telephone', 'text',
            array(
                'name'  => 'telephone',
                'label' => __('Telephone'),
                'id'    => 'address_telephone',
                'title' => __('Telephone'),
                'validation'=> '',
                'ext_type'  => 'TextField'
            )
        );

        $fieldset->addField('fax', 'text',
            array(
                'name'  => 'fax',
                'label' => __('Fax'),
                'id'    => 'address_fax',
                'title' => __('Fax'),
            )
        );
        
        if ($address) {
            $this->setValues($address->toArray());
        }
        
        
        $addressTypes = $address->getAvailableTypes('address_type_id');
        foreach ($addressTypes as $typeId => $info) {
            if (!$address->isPrimary($typeId)) {
                $fieldset->addField('primary_type'.$typeId, 'checkbox',
                    array(
                        'name'  => 'primary_types['.$typeId.']',
                        'label' => __("Use for <strong>%s</strong>", $info['name']),
                        'id'    => 'primary_types_'.$typeId,
                        'title' => __("Use as My Primary <strong>%s</strong> Address", $info['name']),
                        'value' => $typeId,
                        'validation'=> '',
                        'ext_type'  => 'Checkbox'
                    )
                );
            }
        }
    }
}
