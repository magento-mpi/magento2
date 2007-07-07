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
        $this->_initForm();
    }
    
    protected function _initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_account');
        
        $customer = Mage::registry('customer');        
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Account information')));
        
        $this->_setFieldset($customer->getAttributes(), $fieldset);
        
        $form->setValues($customer->getData());
        
        $this->setForm($form);
    }
}
