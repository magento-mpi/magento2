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
        #$this->_initForm();
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $classId = $this->getRequest()->getParam('classId', null);
        $classType = $this->getRequest()->getParam('classType', null);

        $gridCollection = $this->getGridCollection();

        if( $gridCollection ) {
            $indexes = array();
            foreach($gridCollection->getItems() as $item) {
                $indexes[] = $item->getClassGroupId();
            }

            $customerGroups = Mage::getResourceModel('customer/group_collection')
                ->setIgnoreIdFilter($indexes)
                ->load()
                ->toOptionArray();

            if( count($customerGroups) == 0 ) {
                $this->setForm($form);
                return parent::_prepareForm();
            }
        } else {
            $customerGroups = Mage::getResourceModel('customer/group_collection')
                ->load()
                ->toOptionArray();
        }

        if( intval($classId) <= 0 ) {
            $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Customer tax class information')));
            $fieldset->addField('class_name', 'text',
                                array(
                                    'name' => 'class_name',
                                    'label' => __('Class name'),
                                    'title' => __('Class name title'),
                                    'class' => 'required-entry'
                                )
                        );

            $fieldset->addField('class_type', 'hidden',
                                array(
                                    'name' => 'class_type',
                                    'value' => 'CUSTOMER'
                                )
                        );
        } else {
            $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Add customer group')));
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

            $form->setAction(Mage::getUrl("adminhtml/tax_class/saveGroup/classId/{$classId}/classType/{$classType}"));
        } else {
            $form->setAction(Mage::getUrl('adminhtml/tax_class/save'));
        }

        $form->setUseContainer(true);
        $form->setId('class_form');
        $form->setMethod('POST');

        $this->setForm($form);

        return parent::_prepareForm();
    }
}