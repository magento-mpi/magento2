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
class Mage_Adminhtml_Block_Customer_Edit_Tab_Account extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct() 
    {
        parent::__construct();
    }
    
    public function initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_account');
        
        $customer = Mage::registry('customer');        
        $isSubscribed = Mage::getModel('newsletter/subscriber')->loadByCustomer($customer)->isSubscribed(true);
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Account information')));
        
        $this->_setFieldset($customer->getAttributes(), $fieldset);
        if ($customer->getId()) {
            $fieldset->addField('reset_password', 'checkbox',
                array(
                    'label' => __('Reset password'),
                    'name'  => 'reset_password',
                    'value' => '1'
                )
            );            
        }
        else {
            $fieldset->addField('password', 'password',
                array(
                    'label' => __('Password'),
                    'class' => 'input-text required-entry validate-password',
                    'name'  => 'password'
                )
            );
            $fieldset->addField('password_confirm', 'password',
                array(
                    'label' => __('Password confirm'),
                    'class' => 'input-text required-entry validate-cpassword',
                    'name'  => 'password_confirm'
                )
            );
        }
        
        $fieldset->addField('subscription', 'checkbox',
             array(
                    'label' => __('Subscribe to newsletter?'),
                    'name'  => 'subscription'                    
             )
        );
        
        $form->getElement('subscription')->setIsChecked($isSubscribed);
        
        $form->setValues($customer->getData());
        
        $this->setForm($form);
        
        return $this;
    }
}
