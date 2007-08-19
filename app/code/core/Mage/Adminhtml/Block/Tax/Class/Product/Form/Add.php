<?php
/**
 * Admin product tax class add form
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Tax_Class_Product_Form_Add extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setDestElementId('class_form');
        #$this->_initForm();
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $classId = $this->getRequest()->getParam('classId', null);
        $classType = $this->getRequest()->getParam('classType', null);

        if( intval($classId) <= 0 ) {
            $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Product Tax Class Information')));
            $fieldset->addField('class_name', 'text',
                                array(
                                    'name' => 'class_name',
                                    'label' => __('Class Name'),
                                    'class' => 'required-entry',
                                    'required' => true,
                                )
                        );

            $fieldset->addField('class_type', 'hidden',
                                array(
                                    'name' => 'class_type',
                                    'value' => 'PRODUCT'
                                )
                        );
        } else {
            $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Add New Category')));
        }

        if( intval($classId) > 0 ) {
            $fieldset->addField('submit', 'submit',
                                array(
                                    'name' => 'submit',
                                    'value' => __('Add')
                                )
            );

            $fieldset->addField('class_parent_id', 'hidden',
                                array(
                                    'name' => 'class_parent_id',
                                    'value' => $classId,
                                    'no_span' => true
                                )
                        );

            $form->setAction(Mage::getUrl("adminhtml/tax_class/saveGroup/classId/{$classId}/classType/PRODUCT"));
        } else {
            $form->setAction(Mage::getUrl('adminhtml/tax_class/save/classType/PRODUCT'));
        }

        $form->setUseContainer(true);
        $form->setId('class_form');
        $form->setMethod('POST');

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
