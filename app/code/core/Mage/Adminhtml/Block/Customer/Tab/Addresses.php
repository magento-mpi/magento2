<?php
/**
 * Custmer addresses forms
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Customer_Tab_Addresses extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('adminhtml/customer/tab/addresses.phtml');
    }
    
    protected function _beforeToHtml()
    {
        $customerId = (int) $this->getRequest()->getParam('id');
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('address_fieldset', array('legend'=>__('edit customer address')));
        
        $fieldset->addField( 'firstname', 'text',
            array(
                'name'  => 'firstname',
                'label' => __('Firstname'),
                'id'    => 'address_firstname',
                'title' => __('Firstname'),
                'class' => 'required-entry',
            )
        );

        $fieldset->addField('lastname', 'text',
            array(
                'name'  => 'lastname',
                'label' => __('Lastname'),
                'id'    => 'address_lastname',
                'title' => __('Lastname'),
                'class' => 'required-entry',
            )
        );

        $fieldset->addField('company', 'text',
            array(
                'name'  => 'company',
                'label' => __('Company'),
                'id'    => 'address_company',
                'title' => __('Company'),
            )
        );

        $fieldset->addField('country_id', 'select',
            array(
                'name'  => 'country_id',
                'label' => __('Country'),
                'id'    => 'address_country',
                'title' => __('Country'),
                'values'=> Mage::getResourceModel('directory/country_collection')->load()->toOptionArray(),
                'class' => 'required-entry',
            )
        );
        
        $fieldset->addField('region_id', 'select',
            array(
                'name'  => 'region_id',
                'label' => __('State'),
                'id'    => 'address_region_id',
                'title' => __('State'),
                'values'=> Mage::getResourceModel('directory/region_collection')->load()->toOptionArray(),
                'class' => '',
            )
        );

        $fieldset->addField('region', 'text',
            array(
                'name'  => 'region',
                'label' => __('Province'),
                'id'    => 'address_region',
                'title' => __('Province'),
                'class' => 'required-entry',
            )
        );

        $fieldset->addField('city', 'text',
            array(
                'name'  => 'city',
                'label' => __('City'),
                'id'    => 'address_city',
                'title' => __('City'),
                'class' => 'required-entry',
            )
        );

        $fieldset->addField('street', 'textarea',
            array(
                'name'  => 'street',
                'label' => __('Street Address'),
                'id'    => 'address_street',
                'title' => __('Street Address'),
                'class' => 'required-entry',
            )
        );

        $fieldset->addField('postcode', 'text',
            array(
                'name'  => 'postcode',
                'label' => __('Zip/Post Code'),
                'id'    => 'address_postcode',
                'title' => __('Zip/Post code'),
                'class' => 'required-entry',
            )
        );

        $fieldset->addField('telephone', 'text',
            array(
                'name'  => 'telephone',
                'label' => __('Telephone'),
                'id'    => 'address_telephone',
                'title' => __('Telephone'),
                'class' => 'required-entry',
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
        
        /*$address    = Mage::getModel('customer/address_entity');
        $collection = $address->getEmptyCollection();
        
        if ($customerId = (int) $this->getRequest()->getParam('id')) {
            
        }
        
        $this->assign('addressCollection', $collection);
        
        foreach ($address->getAttributeCollection() as $attribute) {
        	$fieldset->addField($attribute->getCode(), 'text', 
                array(
                    'name'  => $attribute->getFormFieldName(),
                    'label' => __($attribute->getCode()),
                    'title' => __($attribute->getCode().' title'),
                    'class' => $attribute->getIsRequired() ? 'required-entry' : '',
                    //'value' => $customer->getData($attribute->getCode())
                )
            );
        }*/
        $addressCollection = Mage::getResourceModel('customer/address_collection');
        $this->assign('addressCollection', $addressCollection);
        $addressCollection->loadByCustomerId($customerId);
        $this->setForm($form);
        return parent::_beforeToHtml();
    }
}
