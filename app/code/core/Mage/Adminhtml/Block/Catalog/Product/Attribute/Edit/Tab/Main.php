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

        $yesno = array(
            array(
                'value' => 0, 
                'label' => __('No')
            ), 
            array(
                'value' => 1, 
                'label' => __('Yes')
            ));

        $fieldset->addField('attribute_code', 'text', array(
            'name'  => 'attribute_code',
            'label' => __('Attribute Code'),
            'title' => __('Attribute Code'),
            'class' => 'validate-code',
            'required' => true,
        ));

        /*$fieldset->addField('default_value', 'text', array(
            'name' => 'default_value',
            'label' => __('Default Value'),
            'title' => __('Default Value'),
        ));*/

        /*$fieldset->addField('frontend_label', 'text', array(
            'name' => 'frontend_label',
            'label' => __('Frontend Label'),
            'title' => __('Frontend Label'),
        ));*/

        $fieldset->addField('frontend_input', 'select', array(
            'name' => 'frontend_input',
            'label' => __('Catalog Input Type'),
            'title' => __('Catalog Input Type'),
            'value' => 'text',
            'values'=>  array(
                array(
                    'value' => 'text', 
                    'label' => __('Text Field')
                ), 
                array(
                    'value' => 'textarea', 
                    'label' => __('Text Area')
                ), 
                array(
                    'value' => 'date', 
                    'label' => __('Date')
                ), 
                array(
                    'value' => 'boolean', 
                    'label' => __('Yes/No')
                ), 
                array(
                    'value' => 'multiselect', 
                    'label' => __('Multiple Select')
                ), 
                array(
                    'value' => 'select', 
                    'label' => __('Dropdown')
                ), 
                array(
                    'value' => 'price', 
                    'label' => __('Price')
                ), 
                array(
                    'value' => 'image', 
                    'label' => __('Image')
                ), 
                array(
                    'value' => 'gallery', 
                    'label' => __('Image Gallery')
                ), 
            )
        ));

        $fieldset->addField('is_unique', 'select', array(
            'name' => 'is_unique',
            'label' => __('Unique Value'),
            'title' => __('Unique Value'),
            'values' => $yesno,
        ));

        $fieldset->addField('is_required', 'select', array(
            'name' => 'is_required',
            'label' => __('Required'),
            'title' => __('Required'),
            'values' => $yesno,
        ));

        $fieldset->addField('frontend_class', 'select', array(
            'name'  => 'frontend_class',
            'label' => __('Input Validation'),
            'title' => __('Input Validation'),
            'values'=>  array(
                array(
                    'value' => '', 
                    'label' => __('None')
                ), 
                array(
                    'value' => 'validate-number', 
                    'label' => __('Decimal Number')
                ), 
                array(
                    'value' => 'validate-digits', 
                    'label' => __('Integer Number')
                ), 
                array(
                    'value' => 'validate-email', 
                    'label' => __('Email')
                ), 
                array(
                    'value' => 'validate-url', 
                    'label' => __('Url')
                ), 
                array(
                    'value' => 'validate-alpha', 
                    'label' => __('Letters')
                ), 
                array(
                    'value' => 'validate-alphanum', 
                    'label' => __('Letters(a-z) or Numbers(0-9)')
                ), 
            )
        ));

        $fieldset->addField('is_searchable', 'select', array(
            'name' => 'is_searchable',
            'label' => __('Searchable'),
            'title' => __('Searchable'),
            'values' => $yesno,
        ));

        $fieldset->addField('is_comparable', 'select', array(
            'name' => 'is_comparable',
            'label' => __('Comparable'),
            'title' => __('Comparable'),
            'values' => $yesno,
        ));

        /*$fieldset->addField('apply_to', 'select', array(
            'name' => 'apply_to',
            'label' => __('Apply To'),
            'title' => __('Apply To'),
            'values' => array(
                array('value' => '0', 'label' => __('All Products')),
                array('value' => '1', 'label' => __('Phisical Products')),
                array('value' => '2', 'label' => __('Virtual Products')),
            ),
        ));*/

        $fieldset->addField('use_in_supre_product', 'select', array(
            'name' => 'use_in_supre_product',
            'label' => __('Apply To Super Product'),
            'title' => __('Apply To Super Product'),
            'values' => $yesno,
        ));

        $fieldset->addField('is_filterable', 'select', array(
            'name' => 'is_filterable',
            'label' => __('Use In Layer Navigation'),
            'title' => __('Use In Layer Navigation'),
            'values' => array(
                array('value' => '0', 'label' => __('No')),
                array('value' => '1', 'label' => __('Fiterable (with results)')),
                array('value' => '2', 'label' => __('Fiterable (no results)')),
            ),
        ));

        if ($model->getIsUserDefined() || !$model->getId()) {
            $fieldset->addField('is_visible_on_front', 'select', array(
                'name' => 'is_visible_on_front',
                'label' => __('Visible In Catalog'),
                'title' => __('Visible In Catalog'),
                'values' => $yesno,
            ));
        }
        
        if ($model->getId()) {
            $form->getElement('attribute_code')->setDisabled(1);
        }
        if (!$model->getIsUserDefined() && $model->getId()) {
            $form->getElement('is_unique')->setDisabled(1);
        }

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }

}