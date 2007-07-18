<?php
/**
 * Admin tax class rname form
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Tax_Class_Form_Rename extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $classId = $this->getRequest()->getParam('classId', null);
        $classType = $this->getRequest()->getParam('classType', null);

        $classObject = Mage::getSingleton('tax/class')->load($classId);

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Rename Class')));

        $fieldset->addField('class_name', 'text',
                            array(
                                'name' => 'class_name',
                                'label' => __('Class Name'),
                                'title' => __('Class Name Title'),
                                'class' => 'required-entry',
                                'value' => $classObject->getClassName(),
                                'no_span' => true
                            )
        );

        $fieldset->addField('submit', 'submit',
                            array(
                                'name' => 'submit',
                                'value' => __('Rename'),
                                'no_span' => true
                            )
        );

        $fieldset->addField('class_id', 'hidden',
                            array(
                                'name' => 'class_id',
                                'value' => $classId,
                                'no_span' => true
                            )
        );

        $fieldset->addField('class_type', 'hidden',
                            array(
                                'name' => 'class_type',
                                'value' => $classType,
                                'no_span' => true
                            )
        );

        $form->setAction(Mage::getUrl('adminhtml/tax_class/save'));
        $form->setUseContainer(true);
        $form->setId('class_rename_form');
        $form->setMethod('POST');
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
