<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product attribute add/edit form main tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $model = Mage::registry('entity_attribute');

        $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post'));

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('catalog')->__('Attribute Properties')));

        $yesno = array(
            array(
                'value' => 0,
                'label' => Mage::helper('catalog')->__('No')
            ),
            array(
                'value' => 1,
                'label' => Mage::helper('catalog')->__('Yes')
            ));

        $fieldset->addField('attribute_code', 'text', array(
            'name'  => 'attribute_code',
            'label' => Mage::helper('catalog')->__('Attribute Identifier<br/>(For internal use. Must be unique with no spaces)'),
            'title' => Mage::helper('catalog')->__('Attribute Identifier'),
            'class' => 'validate-code',
            'required' => true,
        ));

        $fieldset->addField('frontend_input', 'select', array(
            'name' => 'frontend_input',
            'label' => Mage::helper('catalog')->__('Catalog Input Type for Store Owner'),
            'title' => Mage::helper('catalog')->__('Catalog Input Type for Store Owner'),
            'value' => 'text',
            'values'=>  array(
                array(
                    'value' => 'text',
                    'label' => Mage::helper('catalog')->__('Text Field')
                ),
                array(
                    'value' => 'textarea',
                    'label' => Mage::helper('catalog')->__('Text Area')
                ),
                array(
                    'value' => 'date',
                    'label' => Mage::helper('catalog')->__('Date')
                ),
                array(
                    'value' => 'boolean',
                    'label' => Mage::helper('catalog')->__('Yes/No')
                ),
                array(
                    'value' => 'multiselect',
                    'label' => Mage::helper('catalog')->__('Multiple Select')
                ),
                array(
                    'value' => 'select',
                    'label' => Mage::helper('catalog')->__('Dropdown')
                ),
                array(
                    'value' => 'price',
                    'label' => Mage::helper('catalog')->__('Price')
                ),
                array(
                    'value' => 'image',
                    'label' => Mage::helper('catalog')->__('Image')
                ),
            )
        ));






        $fieldset->addField('default_value_text', 'text', array(
            'name' => 'default_value_text',
            'label' => Mage::helper('catalog')->__('Default value'),
            'title' => Mage::helper('catalog')->__('Default value'),
            'value' => $model->getDefaultValue(),
        ));

        $fieldset->addField('default_value_yesno', 'select', array(
            'name' => 'default_value_yesno',
            'label' => Mage::helper('catalog')->__('Default value'),
            'title' => Mage::helper('catalog')->__('Default value'),
            'values' => $yesno,
            'value' => $model->getDefaultValue(),
        ));

        $fieldset->addField('default_value_date', 'date', array(
            'name'  => 'default_value_date',
            'label' => Mage::helper('catalog')->__('Default value'),
            'title' => Mage::helper('catalog')->__('Default value'),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'value' => $model->getDefaultValue(),
        ));

        $fieldset->addField('default_value_textarea', 'textarea', array(
            'name' => 'default_value_textarea',
            'label' => Mage::helper('catalog')->__('Default value'),
            'title' => Mage::helper('catalog')->__('Default value'),
            'value' => $model->getDefaultValue(),
        ));




        $fieldset->addField('is_unique', 'select', array(
            'name' => 'is_unique',
            'label' => Mage::helper('catalog')->__('Unique Value (not shared with other products)'),
            'title' => Mage::helper('catalog')->__('Unique Value (not shared with other products)'),
            'values' => $yesno,
        ));

        $fieldset->addField('is_required', 'select', array(
            'name' => 'is_required',
            'label' => Mage::helper('catalog')->__('Values Required'),
            'title' => Mage::helper('catalog')->__('Values Required'),
            'values' => $yesno,
        ));

        $fieldset->addField('frontend_class', 'select', array(
            'name'  => 'frontend_class',
            'label' => Mage::helper('catalog')->__('Input Validation for Store Owner'),
            'title' => Mage::helper('catalog')->__('Input Validation for Store Owner'),
            'values'=>  array(
                array(
                    'value' => '',
                    'label' => Mage::helper('catalog')->__('None')
                ),
                array(
                    'value' => 'validate-number',
                    'label' => Mage::helper('catalog')->__('Decimal Number')
                ),
                array(
                    'value' => 'validate-digits',
                    'label' => Mage::helper('catalog')->__('Integer Number')
                ),
                array(
                    'value' => 'validate-email',
                    'label' => Mage::helper('catalog')->__('Email')
                ),
                array(
                    'value' => 'validate-url',
                    'label' => Mage::helper('catalog')->__('Url')
                ),
                array(
                    'value' => 'validate-alpha',
                    'label' => Mage::helper('catalog')->__('Letters')
                ),
                array(
                    'value' => 'validate-alphanum',
                    'label' => Mage::helper('catalog')->__('Letters(a-z) or Numbers(0-9)')
                ),
            )
        ));

        /*$fieldset->addField('apply_to', 'select', array(
            'name' => 'apply_to',
            'label' => Mage::helper('catalog')->__('Apply To'),
            'title' => Mage::helper('catalog')->__('Apply To'),
            'values' => array(
                array('value' => '0', 'label' => Mage::helper('catalog')->__('All Products')),
                array('value' => '1', 'label' => Mage::helper('catalog')->__('Physical Products')),
                array('value' => '2', 'label' => Mage::helper('catalog')->__('Virtual Products')),
            ),
        ));*/

        $fieldset->addField('use_in_super_product', 'select', array(
            'name' => 'use_in_super_product',
            'label' => Mage::helper('catalog')->__('Apply To Configurable/Grouped Product'),
            'values' => $yesno,
        ));
        // -----


        // frontend properties fieldset
        $fieldset = $form->addFieldset('front_fieldset', array('legend'=>Mage::helper('catalog')->__('Frontend Properties')));

        $fieldset->addField('is_searchable', 'select', array(
            'name' => 'is_searchable',
            'label' => Mage::helper('catalog')->__('Use in quick search'),
            'title' => Mage::helper('catalog')->__('Use in quick search'),
            'values' => $yesno,
        ));

        $fieldset->addField('is_visible_in_advanced_search', 'select', array(
            'name' => 'is_visible_in_advanced_search',
            'label' => Mage::helper('catalog')->__('Use in advanced search'),
            'title' => Mage::helper('catalog')->__('Use in advanced search'),
            'values' => $yesno,
        ));

        $fieldset->addField('is_comparable', 'select', array(
            'name' => 'is_comparable',
            'label' => Mage::helper('catalog')->__('Comparable on Front-end'),
            'title' => Mage::helper('catalog')->__('Comparable on Front-end'),
            'values' => $yesno,
        ));


        $fieldset->addField('is_filterable', 'select', array(
            'name' => 'is_filterable',
            'label' => Mage::helper('catalog')->__("Use In Layered Navigation<br/>(Can be used only with catalog input type 'Dropdown')"),
            'title' => Mage::helper('catalog')->__('Can be used only with catalog input type Dropdown'),
            'values' => array(
                array('value' => '0', 'label' => Mage::helper('catalog')->__('No')),
                array('value' => '1', 'label' => Mage::helper('catalog')->__('Filterable (with results)')),
                array('value' => '2', 'label' => Mage::helper('catalog')->__('Filterable (no results)')),
            ),
        ));

        if ($model->getIsUserDefined() || !$model->getId()) {
            $fieldset->addField('is_visible_on_front', 'select', array(
                'name' => 'is_visible_on_front',
                'label' => Mage::helper('catalog')->__('Visible on Catalog Pages on Front-end'),
                'title' => Mage::helper('catalog')->__('Visible on Catalog Pages on Front-end'),
                'values' => $yesno,
            ));
        }
        // -----




        // system properties fieldset
        $fieldset = $form->addFieldset('system_fieldset', array('legend'=>Mage::helper('catalog')->__('System Properties')));

        if ($model->getAttributeId()) {
            $fieldset->addField('attribute_id', 'hidden', array(
                'name' => 'attribute_id',
            ));
        }

        /*$fieldset->addField('attribute_model', 'text', array(
            'name' => 'attribute_model',
            'label' => Mage::helper('catalog')->__('Attribute Model'),
            'title' => Mage::helper('catalog')->__('Attribute Model'),
        ));

        $fieldset->addField('backend_model', 'text', array(
            'name' => 'backend_model',
            'label' => Mage::helper('catalog')->__('Backend Model'),
            'title' => Mage::helper('catalog')->__('Backend Model'),
        ));*/
/*
        $fieldset->addField('backend_type', 'select', array(
            'name' => 'backend_type',
            'label' => Mage::helper('catalog')->__('Data Type for Saving in Database'),
            'title' => Mage::helper('catalog')->__('Data Type for Saving in Database'),
            'options' => array(
                'text'      => Mage::helper('catalog')->__('Text'),
                'varchar'   => Mage::helper('catalog')->__('Varchar'),
                'static'    => Mage::helper('catalog')->__('Static'),
                'datetime'  => Mage::helper('catalog')->__('Datetime'),
                'decimal'   => Mage::helper('catalog')->__('Decimal'),
                'int'       => Mage::helper('catalog')->__('Integer'),
            ),
        ));
*/
        /*$fieldset->addField('backend_table', 'text', array(
            'name' => 'backend_table',
            'label' => Mage::helper('catalog')->__('Backend Table'),
            'title' => Mage::helper('catalog')->__('Backend Table Title'),
        ));

        $fieldset->addField('frontend_model', 'text', array(
            'name' => 'frontend_model',
            'label' => Mage::helper('catalog')->__('Frontend Model'),
            'title' => Mage::helper('catalog')->__('Frontend Model'),
        ));*/

        /*$fieldset->addField('is_visible', 'select', array(
            'name' => 'is_visible',
            'label' => Mage::helper('catalog')->__('Visible'),
            'title' => Mage::helper('catalog')->__('Visible'),
            'values' => $yesno,
        ));*/

        /*$fieldset->addField('source_model', 'text', array(
            'name' => 'source_model',
            'label' => Mage::helper('catalog')->__('Source Model'),
            'title' => Mage::helper('catalog')->__('Source Model'),
        ));*/

        $globalTypes = array(
            0=>Mage::helper('catalog')->__('Store'),
            2=>Mage::helper('catalog')->__('Website'),
            1=>Mage::helper('catalog')->__('Global'),
            );

        $fieldset->addField('is_global', 'select', array(
            'name'  => 'is_global',
            'label' => Mage::helper('catalog')->__('Scope'),
            'title' => Mage::helper('catalog')->__('Scope'),
            'values'=> $globalTypes,
        ));
        // -----



        if ($model->getId()) {
            $form->getElement('attribute_code')->setDisabled(1);
            //$form->getElement('backend_type')->setDisabled(1);
            $form->getElement('frontend_input')->setDisabled(1);
        }
        if (!$model->getIsUserDefined() && $model->getId()) {
            $form->getElement('is_unique')->setDisabled(1);
        }


        $form->addValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }

}
