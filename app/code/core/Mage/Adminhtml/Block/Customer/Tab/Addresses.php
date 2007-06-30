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
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('address_fieldset', array('legend'=>__('edit customer address')));
        
        $address    = Mage::getModel('customer/address_entity');
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
        }
        
        $this->setForm($form);
        return parent::_beforeToHtml();
    }
}
