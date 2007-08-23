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

class Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_Front extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $model = Mage::registry('entity_attribute');

        $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'POST'));

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Frontend Properties')));

        $yesno = array(
            array(
                'value' => 0,
                'label' => __('No')
            ),
            array(
                'value' => 1,
                'label' => __('Yes')
            ));


        $fieldset->addField('is_searchable', 'select', array(
            'name' => 'is_searchable',
            'label' => __('Searchable on Front-end'),
            'title' => __('Searchable on Front-end'),
            'values' => $yesno,
        ));

        $fieldset->addField('is_comparable', 'select', array(
            'name' => 'is_comparable',
            'label' => __('Comparable on Front-end'),
            'title' => __('Comparable on Front-end'),
            'values' => $yesno,
        ));


        $fieldset->addField('is_filterable', 'select', array(
            'name' => 'is_filterable',
            'label' => __('Use In Layered Navigation<br/>(Can be used only with catalog input type "Dropdown")'),
            'title' => __('Can be used only with catalog input type Dropdown'),
            'values' => array(
                array('value' => '0', 'label' => __('No')),
                array('value' => '1', 'label' => __('Fiterable (with results)')),
                array('value' => '2', 'label' => __('Fiterable (no results)')),
            ),
        ));

        if ($model->getIsUserDefined() || !$model->getId()) {
            $fieldset->addField('is_visible_on_front', 'select', array(
                'name' => 'is_visible_on_front',
                'label' => __('Visible on Catalog Pages on Front-end'),
                'title' => __('Visible on Catalog Pages on Front-end'),
                'values' => $yesno,
            ));
        }

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }

}