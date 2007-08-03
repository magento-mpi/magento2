<?php
/**
 * Product attribute add/edit form
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('attribute_form');
        $this->setTitle(__('Attribute Information'));
    }

    protected function _prepareForm()
    {
        $attribute = Mage::registry('entity_attribute');

        $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'POST'));

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Attribute Properties')));

        if ($attribute->getAttributeId()) {
            $fieldset->addField('attribute_id', 'hidden', array(
                'name' => 'attribute_id',
            ));
        }

        $yesno = array(array('value' => 0, 'label' => __('No')), array('value' => 1, 'label' => __('Yes')));

        $fieldset->addField('attribute_code', 'text', array(
            'name' => 'attribute_code',
            'label' => __('Attribute Code'),
            'title' => __('Attribute Code'),
            'required' => $attribute->getAttributeId() ? false : true,
        ));

        $fieldset->addField('default_value', 'text', array(
            'name' => 'default_value',
            'label' => __('Default Value'),
            'title' => __('Default Value'),
        ));

        $fieldset->addField('attribute_model', 'text', array(
            'name' => 'attribute_model',
            'label' => __('Attribute Model'),
            'title' => __('Attribute Model'),
        ));

        $fieldset->addField('backend_model', 'text', array(
            'name' => 'backend_model',
            'label' => __('Backend Model'),
            'title' => __('Backend Model'),
        ));

        $fieldset->addField('backend_type', 'select', array(
            'name' => 'backend_type',
            'label' => __('Backend Type'),
            'title' => __('Backend Type'),
            'options' => array(
                'text' => __('Text'),
                'varchar' => __('Varchar'),
                'static' => __('Static'),
                'datetime' => __('Datetime'),
                'decimal' => __('Decimal'),
                'int' => __('Integer'),
            ),
        ));

        $fieldset->addField('backend_table', 'text', array(
            'name' => 'backend_table',
            'label' => __('Backend Table'),
            'title' => __('Backend Table Title'),
        ));

        $fieldset->addField('frontend_model', 'text', array(
            'name' => 'frontend_model',
            'label' => __('Frontend Model'),
            'title' => __('Frontend Model'),
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

        $fieldset->addField('is_visible', 'select', array(
            'name' => 'is_visible',
            'label' => __('Visible'),
            'title' => __('Visible'),
            'values' => $yesno,
        ));

        $fieldset->addField('frontend_class', 'text', array(
            'name' => 'frontend_class',
            'label' => __('Frontend Class'),
            'title' => __('Frontend Class'),
        ));

        $fieldset->addField('source_model', 'text', array(
            'name' => 'source_model',
            'label' => __('Source Model'),
            'title' => __('Source Model'),
        ));

        $fieldset->addField('is_global', 'select', array(
            'name' => 'is_global',
            'label' => __('Global'),
            'title' => __('Global'),
            'values' => $yesno,
        ));

        $fieldset->addField('is_required', 'select', array(
            'name' => 'is_required',
            'label' => __('Required'),
            'title' => __('Required'),
            'values' => $yesno,
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

        $form->setValues($attribute->getData());

        if ($attribute->getAttributeId()) {
            $form->getElement('attribute_code')->setDisabled(1);
            if ($attribute->getIsGlobal()) {
                $form->getElement('is_global')->setDisabled(1);
            }
            // TOFIX the logic
            if ( $attribute->getBackendType() ) {
                $form->getElement('backend_type')->setDisabled('true');
            }
            if ( $attribute->getBackendTable() ) {
                $form->getElement('backend_table')->setDisabled('true');
            }
        } else {
            // TOFIX
            $form->getElement('is_visible')->setValue(1);
        }

        $form->setUseContainer(true);

        $this->setForm($form);

        return parent::_prepareForm();
    }

}