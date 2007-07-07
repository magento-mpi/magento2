<?php
/**
 * Admin customer tax class add form
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Tax_Class_Customer_Form_Add extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setDestElementId('class_form');
        $this->_initForm();
    }

    protected function _initForm()
    {
        $form = new Varien_Data_Form();
        $classId = $this->getRequest()->getParam('classId', null);

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('New customer tax class information')));

        Varien_Profiler::start('classForm');

        $customerGroups = Mage::getResourceModel('customer/group_collection')->load()->toOptionArray();
        if( intval($classId) <= 0 ) {
            $fieldset->addField('class_name', 'text',
                                array(
                                    'name' => 'class_name',
                                    'label' => __('Class name'),
                                    'title' => __('Class name title'),
                                    'class' => 'required-entry'
                                )
                        );
        } else {
            $customerGroupsInUse = Mage::getResourceModel('tax/class_customer_collection');
            $customerGroupsInUse->getFilterByClassId($classId)
        }

        $fieldset->addField('class_group', 'select',
                            array(
                                'name' => 'class_group',
                                'label' => __('Customer group'),
                                'title' => __('Customer group title'),
                                'class' => 'required-entry',
                                'values' => $customerGroups
                            )
        );

        if( intval($classId) > 0 ) {
            $fieldset->addField('submit', 'submit',
                                array(
                                    'name' => 'submit',
                                    'value' => __('Add')
                                )
            );

            $fieldset->addField('class_id', 'hidden',
                                array(
                                    'name' => 'class_id',
                                    'value' => $classId
                                )
                        );

            $form->setAction(Mage::getUrl('adminhtml/tax_class_customer/saveGroup'));
        } else {
            $form->setAction(Mage::getUrl('adminhtml/tax_class_customer/save'));
        }

        $form->setUseContainer(true);
        $form->setId('class_form');
        $form->setMethod('POST');

        Varien_Profiler::stop('classForm');
        $this->setForm($form);
    }
}