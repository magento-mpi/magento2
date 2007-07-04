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
        $form->setHtmlIdPrefix('_account');
        
        $customer = Mage::registry('customer');        
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Account information')));
        
        foreach ($customer->getAttributes() as $attribute) {
            if ($inputType = $attribute->getFrontend()->getInputType()) {
                $element = $fieldset->addField($attribute->getName(), $inputType,
                    array(
                        'name'  => $attribute->getName(),
                        'label' => $attribute->getFrontend()->getConfigField('label'),
                        'class' => $attribute->getFrontend()->getConfigField('class')
                    )
                );
                if ($inputType == 'select') {
                    $element->setValues($attribute->getFrontend()->getSelectOptions());
                }
            }
        }
        
        $form->setValues($customer->getData());
        
        $this->setForm($form);
    }
}
