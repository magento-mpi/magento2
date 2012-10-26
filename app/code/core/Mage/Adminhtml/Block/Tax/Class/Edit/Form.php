<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml Tax Class Edit Form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Tax_Class_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _construct()
    {
        parent::_construct();

        $this->setId('taxClassForm');
    }

    protected function _prepareForm()
    {
        $model  = Mage::registry('tax_class');
        $form   = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getData('action'),
            'method'    => 'post'
        ));

        $classType  = $this->getClassType();

        $this->setTitle($classType == Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER
            ? Mage::helper('Mage_Cms_Helper_Data')->__('Customer Tax Class Information')
            : Mage::helper('Mage_Cms_Helper_Data')->__('Product Tax Class Information')
        );

        $fieldset   = $form->addFieldset('base_fieldset', array(
            'legend'    => $classType == Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER
                ? Mage::helper('Mage_Tax_Helper_Data')->__('Customer Tax Class Information')
                : Mage::helper('Mage_Tax_Helper_Data')->__('Product Tax Class Information')
        ));

        $fieldset->addField('class_name', 'text',
            array(
                'name'  => 'class_name',
                'label' => Mage::helper('Mage_Tax_Helper_Data')->__('Class Name'),
                'class' => 'required-entry',
                'value' => $model->getClassName(),
                'required' => true,
            )
        );

        $fieldset->addField('class_type', 'hidden',
            array(
                'name'      => 'class_type',
                'value'     => $classType,
                'no_span'   => true
            )
        );

        if ($model->getId()) {
            $fieldset->addField('class_id', 'hidden',
                array(
                    'name'      => 'class_id',
                    'value'     => $model->getId(),
                    'no_span'   => true
                )
            );
        }

        $form->setAction($this->getUrl('*/tax_class/save'));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
