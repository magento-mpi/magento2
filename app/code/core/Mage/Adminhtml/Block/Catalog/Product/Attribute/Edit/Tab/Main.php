<?php
/**
 * Product attribute add/edit form main tab
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $model = Mage::registry('entity_attribute');

        $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'POST'));

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('General Information')));

        $yesno = array(array('value' => 0, 'label' => __('No')), array('value' => 1, 'label' => __('Yes')));

        $fieldset->addField('attribute_code', 'text', array(
            'name' => 'attribute_code',
            'label' => __('Attribute Code'),
            'title' => __('Attribute Code'),
            'required' => $model->getAttributeId() ? false : true,
        ));

        $fieldset->addField('default_value', 'text', array(
            'name' => 'default_value',
            'label' => __('Default Value'),
            'title' => __('Default Value'),
        ));

        $fieldset->addField('frontend_input', 'text', array(
            'name' => 'frontend_input',
            'label' => __('Frontend Input'),
            'title' => __('Frontend Input'),
        ));

        $fieldset->addField('frontend_label', 'text', array(
            'name' => 'frontend_label',
            'label' => __('Frontend Label'),
            'title' => __('Frontend Label'),
        ));

        $fieldset->addField('frontend_class', 'text', array(
            'name' => 'frontend_class',
            'label' => __('Frontend Class'),
            'title' => __('Frontend Class'),
        ));

        $fieldset->addField('is_searchable', 'select', array(
            'name' => 'is_searchable',
            'label' => __('Searchable'),
            'title' => __('Searchable'),
            'values' => $yesno,
        ));

        $fieldset->addField('is_filterable', 'select', array(
            'name' => 'is_filterable',
            'label' => __('Filterable'),
            'title' => __('Filterable'),
            'values' => array(
                array('value' => '0', 'label' => __('No')),
                array('value' => '1', 'label' => __('Fiterable (with results)')),
                array('value' => '2', 'label' => __('Fiterable (no results)')),
            ),
        ));

        $fieldset->addField('is_comparable', 'select', array(
            'name' => 'is_comparable',
            'label' => __('Comparable'),
            'title' => __('Comparable'),
            'values' => $yesno,
        ));

        if ($model->getAttributeId()) {
            $form->getElement('attribute_code')->setDisabled(1);
        }

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }

}