<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product attribute add/edit form system tab
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_System extends Magento_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $model = Mage::registry('entity_attribute');

        $form = new Magento_Data_Form();
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('Magento_Catalog_Helper_Data')->__('System Properties')));

        if ($model->getAttributeId()) {
            $fieldset->addField('attribute_id', 'hidden', array(
                'name' => 'attribute_id',
            ));
        }

        $yesno = array(
            array(
                'value' => 0,
                'label' => Mage::helper('Magento_Catalog_Helper_Data')->__('No')
            ),
            array(
                'value' => 1,
                'label' => Mage::helper('Magento_Catalog_Helper_Data')->__('Yes')
            ));

        /*$fieldset->addField('attribute_model', 'text', array(
            'name' => 'attribute_model',
            'label' => Mage::helper('Magento_Catalog_Helper_Data')->__('Attribute Model'),
            'title' => Mage::helper('Magento_Catalog_Helper_Data')->__('Attribute Model'),
        ));

        $fieldset->addField('backend_model', 'text', array(
            'name' => 'backend_model',
            'label' => Mage::helper('Magento_Catalog_Helper_Data')->__('Backend Model'),
            'title' => Mage::helper('Magento_Catalog_Helper_Data')->__('Backend Model'),
        ));*/

        $fieldset->addField('backend_type', 'select', array(
            'name' => 'backend_type',
            'label' => Mage::helper('Magento_Catalog_Helper_Data')->__('Data Type for Saving in Database'),
            'title' => Mage::helper('Magento_Catalog_Helper_Data')->__('Data Type for Saving in Database'),
            'options' => array(
                'text'      => Mage::helper('Magento_Catalog_Helper_Data')->__('Text'),
                'varchar'   => Mage::helper('Magento_Catalog_Helper_Data')->__('Varchar'),
                'static'    => Mage::helper('Magento_Catalog_Helper_Data')->__('Static'),
                'datetime'  => Mage::helper('Magento_Catalog_Helper_Data')->__('Datetime'),
                'decimal'   => Mage::helper('Magento_Catalog_Helper_Data')->__('Decimal'),
                'int'       => Mage::helper('Magento_Catalog_Helper_Data')->__('Integer'),
            ),
        ));

        /*$fieldset->addField('backend_table', 'text', array(
            'name' => 'backend_table',
            'label' => Mage::helper('Magento_Catalog_Helper_Data')->__('Backend Table'),
            'title' => Mage::helper('Magento_Catalog_Helper_Data')->__('Backend Table Title'),
        ));

        $fieldset->addField('frontend_model', 'text', array(
            'name' => 'frontend_model',
            'label' => Mage::helper('Magento_Catalog_Helper_Data')->__('Frontend Model'),
            'title' => Mage::helper('Magento_Catalog_Helper_Data')->__('Frontend Model'),
        ));*/

        /*$fieldset->addField('is_visible', 'select', array(
            'name' => 'is_visible',
            'label' => Mage::helper('Magento_Catalog_Helper_Data')->__('Visible'),
            'title' => Mage::helper('Magento_Catalog_Helper_Data')->__('Visible'),
            'values' => $yesno,
        ));*/

        /*$fieldset->addField('source_model', 'text', array(
            'name' => 'source_model',
            'label' => Mage::helper('Magento_Catalog_Helper_Data')->__('Source Model'),
            'title' => Mage::helper('Magento_Catalog_Helper_Data')->__('Source Model'),
        ));*/

        $fieldset->addField('is_global', 'select', array(
            'name'  => 'is_global',
            'label' => Mage::helper('Magento_Catalog_Helper_Data')->__('Globally Editable'),
            'title' => Mage::helper('Magento_Catalog_Helper_Data')->__('Globally Editable'),
            'values'=> $yesno,
        ));

        $form->setValues($model->getData());

        if ($model->getAttributeId()) {
            $form->getElement('backend_type')->setDisabled(1);
            if ($model->getIsGlobal()) {
                #$form->getElement('is_global')->setDisabled(1);
            }
        } else {
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }

}
