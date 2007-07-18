<?php
/**
 * Category edit general tab
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Catalog_Category_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct() 
    {
        parent::__construct();
    }
    
    public function _initChildren()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_general');
        
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('General information')));
        $category = Mage::getModel('catalog/category')
            ->load(1);
        
        $this->_setFieldset($category->getAttributes(), $fieldset);
        
        /*if ($balanceElement = $form->getElement('store_balance')) {
            $balanceElement->setValueFilter(new Varien_Filter_Sprintf('%s', 2, '.', ''));
        }
        
        if ($customer->getId()) {
            $fieldset->addField('reset_password', 'checkbox',
                array(
                    'label' => __('Reset Password'),
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
                    'label' => __('Password Confirmation'),
                    'class' => 'input-text required-entry validate-cpassword',
                    'name'  => 'password_confirm'
                )
            );
        }*/
        
        
        //$form->setValues($customer->getData());
        
        $this->setForm($form);
        
        return $this;
    }
}
