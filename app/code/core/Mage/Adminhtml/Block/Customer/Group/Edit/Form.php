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
        
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Group information')));
        
        $fieldset->addField('code', 'text', 
            array(
                'name'  => 'code',
                'label' => __('group name'),
                'title' => __('group name title'),
                'class' => 'required-entry',
                'value' => $customerGroup->getCode()
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