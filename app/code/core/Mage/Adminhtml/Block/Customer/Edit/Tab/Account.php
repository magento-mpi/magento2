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
        $form->setFieldNameSuffix('account');

        $customer = Mage::registry('customer');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Account Information')));

        $this->_setFieldset($customer->getAttributes(), $fieldset);

        if ($customer->getId()) {
            $form->getElement('created_in')->setDisabled(true);
            $form->getElement('store_id')->setType('hidden');
        } else {
            $fieldset->removeField('created_in');
        }

        if ($balanceElement = $form->getElement('store_balance')) {
            $balanceElement->setValueFilter(new Varien_Filter_Sprintf('%s', 2, '.', ''));
        }

        if ($customer->getId()) {
            $newFieldset = $form->addFieldset('password_fieldset', array('legend'=>__('Password Management')));
            // New customer password
            $field = $newFieldset->addField('new_password', 'text',
                array(
                    'label' => __('New Password'),
                    'name'  => 'new_password',
                    'class' => 'validate-new-password'
                )
            );
            $field->setRenderer($this->getLayout()->createBlock('adminhtml/customer_edit_renderer_newpass'));
        }
        else {
            $fieldset->addField('password', 'password',
                array(
                    'label' => __('Password'),
                    'class' => 'input-text required-entry validate-password',
                    'name'  => 'password',
                    'required' => true
                )
            );
            $fieldset->addField('password_confirm', 'password',
                array(
                    'label' => __('Password Confirmation'),
                    'class' => 'input-text required-entry validate-cpassword',
                    'name'  => 'password_confirm',
                    'required' => true
                )
            );
        }


        $form->setValues($customer->getData());

        $this->setForm($form);

        return $this;
    }
}
