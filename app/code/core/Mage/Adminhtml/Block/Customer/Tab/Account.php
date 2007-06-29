<?php
/**
 * Customer account form block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Customer_Tab_Account extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct() 
    {
        parent::__construct();
        $this->_initForm();
    }
    
    protected function _initForm()
    {
        $form = new Varien_Data_Form();
        
        $customer = Mage::getModel('customer/entity');
        if ($customerId = (int) $this->_request->getParam('id')) {
            $customer->load($customerId);
        }
            
            
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Account information')));
        
        foreach ($customer->getAttributeCollection() as $attribute) {
        	$fieldset->addField($attribute->getCode(), 'text', 
                array(
                    'name'  => $attribute->getFormFieldName(),
                    'label' => __($attribute->getCode()),
                    'title' => __($attribute->getCode().' title'),
                    'class' => $attribute->getIsRequired() ? 'required-entry' : '',
                    'value' => $customer->getData($attribute->getCode())
                )
            );
        }
        
        if ($element = $form->getElement('password')) {
            $element->setType('password');
            $element->setClass('required-entry validate-password');
            
            if ($customer->getId()) {
                $element->setLabel(__('new password'));
                $element->setTitle(__('new password title'));
            }
            else {
            	$fieldset->addField('confirmation', 'password', 
                    array(
                        'name'  => 'password_confirm',
                        'label' => __('password confirm'),
                        'title' => __('password confirm title'),
                        'class' => 'required-entry validate-cpassword',
                        'value' => $customer->getData($attribute->getCode())
                    ),
                    'password'
                );
            }
        }
        if ($element = $form->getElement('email')) {
            $element->setClass('required-entry validate-email');
        }
        
        
        $this->setForm($form);
    }
}
