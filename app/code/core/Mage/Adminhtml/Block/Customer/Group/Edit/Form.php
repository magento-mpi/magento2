<?php
/** 
 * Adminhtml customer groups edit form
 * 
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Adminhtml_Block_Customer_Group_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /** 
     * Constructor
     * 
     * Initialize form
     */
    public function __construct() 
    {
        parent::__construct();
        $this->_renderPrepare();
    }
   
    /**
     * Prepare form for render
     */
    protected function _renderPrepare()
    {
        $form = new Varien_Data_Form();
        $customerGroup = Mage::getModel('customer/group');
        if ($groupId = (int) $this->_request->getParam('id')) {
            $customerGroup->load($groupId);
        }
        
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Group Information')));
        
        $fieldset->addField('code', 'text', 
            array(
                'name'  => 'code',
                'label' => __('Group Name'),
                'title' => __('Group Name'),
                'class' => 'required-entry',
                'required' => true,
                'value' => $customerGroup->getCode()
            )
        );
        
        $fieldset->addField('tax_class', 'select', 
            array(
                'name'  => 'tax_class',
                'label' => __('Tax class'),
                'title' => __('Tax class'),
                'class' => 'required-entry',
                'required' => true,
                'value' => $customerGroup->getTaxClass(),
                'values' => Mage::getSingleton('tax/class_source_customer')->toOptionArray()
            )
        );
        
        if ($customerGroup->getId()) { 
            // If edit add id
            $form->addField('id', 'hidden', 
                array(
                    'name'  => 'id',
                    'value' => $customerGroup->getId()
                )
            );
        }
        
        $this->setForm($form);
    }
    
    
}
