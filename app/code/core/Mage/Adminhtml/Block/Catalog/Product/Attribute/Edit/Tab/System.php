<?php
/**
 * Product attribute add/edit form system tab
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_System extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $model = Mage::registry('entity_attribute');

        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('System Properties')));

        if ($model->getAttributeId()) {
            $fieldset->addField('attribute_id', 'hidden', array(
                'name' => 'attribute_id',
            ));
        }

        $yesno = array(
            array(
                'value' => 0, 
                'label' => __('No')
            ), 
            array(
                'value' => 1, 
                'label' => __('Yes')
            ));

        /*$fieldset->addField('attribute_model', 'text', array(
            'name' => 'attribute_model',
            'label' => __('Attribute Model'),
            'title' => __('Attribute Model'),
        ));

        $fieldset->addField('backend_model', 'text', array(
            'name' => 'backend_model',
            'label' => __('Backend Model'),
            'title' => __('Backend Model'),
        ));*/

        $fieldset->addField('backend_type', 'select', array(
            'name' => 'backend_type',
            'label' => __('Data Type'),
            'title' => __('Data Type'),
            'options' => array(
                'text'      => __('Text'),
                'varchar'   => __('Varchar'),
                'static'    => __('Static'),
                'datetime'  => __('Datetime'),
                'decimal'   => __('Decimal'),
                'int'       => __('Integer'),
            ),
        ));

        /*$fieldset->addField('backend_table', 'text', array(
            'name' => 'backend_table',
            'label' => __('Backend Table'),
            'title' => __('Backend Table Title'),
        ));

        $fieldset->addField('frontend_model', 'text', array(
            'name' => 'frontend_model',
            'label' => __('Frontend Model'),
            'title' => __('Frontend Model'),
        ));*/

        /*$fieldset->addField('is_visible', 'select', array(
            'name' => 'is_visible',
            'label' => __('Visible'),
            'title' => __('Visible'),
            'values' => $yesno,
        ));*/

        /*$fieldset->addField('source_model', 'text', array(
            'name' => 'source_model',
            'label' => __('Source Model'),
            'title' => __('Source Model'),
        ));*/

        $fieldset->addField('is_global', 'select', array(
            'name'  => 'is_global',
            'label' => __('Global'),
            'title' => __('Global'),
            'values'=> $yesno,
        ));

        $fieldset->addField('is_required', 'select', array(
            'name' => 'is_required',
            'label' => __('Required'),
            'title' => __('Required'),
            'values' => $yesno,
        ));

        $form->setValues($model->getData());

        if ($model->getAttributeId()) {
            if ($model->getIsGlobal()) {
                $form->getElement('is_global')->setDisabled(1);
            }
            // TOFIX the logic
            if ( $model->getBackendType() ) {
                $form->getElement('backend_type')->setDisabled('true');
            }
            if ( $model->getBackendTable() ) {
                $form->getElement('backend_table')->setDisabled('true');
            }
        } else {
            // TOFIX
            //$form->getElement('is_visible')->setValue(1);
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }

}