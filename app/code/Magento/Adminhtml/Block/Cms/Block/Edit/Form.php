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
 * Adminhtml cms block edit form
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Cms_Block_Edit_Form extends Magento_Adminhtml_Block_Widget_Form
{

    /**
     * Init form
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('block_form');
        $this->setTitle(Mage::helper('Magento_Cms_Helper_Data')->__('Block Information'));
    }

    /**
     * Load Wysiwyg on demand and Prepare layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('Magento_Cms_Model_Wysiwyg_Config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('cms_block');

        $form = new Magento_Data_Form(
            array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post')
        );

        $form->setHtmlIdPrefix('block_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('Magento_Cms_Helper_Data')->__('General Information'), 'class' => 'fieldset-wide'));

        if ($model->getBlockId()) {
            $fieldset->addField('block_id', 'hidden', array(
                'name' => 'block_id',
            ));
        }

        $fieldset->addField('title', 'text', array(
            'name'      => 'title',
            'label'     => Mage::helper('Magento_Cms_Helper_Data')->__('Block Title'),
            'title'     => Mage::helper('Magento_Cms_Helper_Data')->__('Block Title'),
            'required'  => true,
        ));

        $fieldset->addField('identifier', 'text', array(
            'name'      => 'identifier',
            'label'     => Mage::helper('Magento_Cms_Helper_Data')->__('Identifier'),
            'title'     => Mage::helper('Magento_Cms_Helper_Data')->__('Identifier'),
            'required'  => true,
            'class'     => 'validate-xml-identifier',
        ));

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $field =$fieldset->addField('store_id', 'multiselect', array(
                'name'      => 'stores[]',
                'label'     => Mage::helper('Magento_Cms_Helper_Data')->__('Store View'),
                'title'     => Mage::helper('Magento_Cms_Helper_Data')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('Magento_Core_Model_System_Store')->getStoreValuesForForm(false, true),
            ));
            $renderer = $this->getLayout()->createBlock('Magento_Backend_Block_Store_Switcher_Form_Renderer_Fieldset_Element');
            $field->setRenderer($renderer);
        }
        else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'stores[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }

        $fieldset->addField('is_active', 'select', array(
            'label'     => Mage::helper('Magento_Cms_Helper_Data')->__('Status'),
            'title'     => Mage::helper('Magento_Cms_Helper_Data')->__('Status'),
            'name'      => 'is_active',
            'required'  => true,
            'options'   => array(
                '1' => Mage::helper('Magento_Cms_Helper_Data')->__('Enabled'),
                '0' => Mage::helper('Magento_Cms_Helper_Data')->__('Disabled'),
            ),
        ));
        if (!$model->getId()) {
            $model->setData('is_active', '1');
        }

        $fieldset->addField('content', 'editor', array(
            'name'      => 'content',
            'label'     => Mage::helper('Magento_Cms_Helper_Data')->__('Content'),
            'title'     => Mage::helper('Magento_Cms_Helper_Data')->__('Content'),
            'style'     => 'height:36em',
            'required'  => true,
            'config'    => Mage::getSingleton('Magento_Cms_Model_Wysiwyg_Config')->getConfig()
        ));

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
