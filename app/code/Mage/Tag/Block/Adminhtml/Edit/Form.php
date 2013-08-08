<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml tag edit form
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Tag_Block_Adminhtml_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('tag_form');
        $this->setTitle(__('Block Information'));
    }

    /**
     * Prepare form
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $model = Mage::registry('tag_tag');

        $form = new Varien_Data_Form(
            array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post')
        );

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend'=>__('General Information')));

        if ($model->getTagId()) {
            $fieldset->addField('tag_id', 'hidden', array(
                'name' => 'tag_id',
            ));
        }

        $fieldset->addField('form_key', 'hidden', array(
            'name'  => 'form_key',
            'value' => Mage::getSingleton('Mage_Core_Model_Session')->getFormKey(),
        ));

        $fieldset->addField('store_id', 'hidden', array(
            'name'  => 'store_id',
            'value' => (int)$this->getRequest()->getParam('store')
        ));

        $fieldset->addField('name', 'text', array(
            'name' => 'tag_name',
            'label' => __('Tag Name'),
            'title' => __('Tag Name'),
            'required' => true,
            'scope_label' => ' ' . __('[GLOBAL]'),
        ));

        $fieldset->addField('status', 'select', array(
            'label' => __('Status'),
            'title' => __('Status'),
            'name' => 'tag_status',
            'required' => true,
            'options' => array(
                Mage_Tag_Model_Tag::STATUS_DISABLED => __('Disabled'),
                Mage_Tag_Model_Tag::STATUS_PENDING  => __('Pending'),
                Mage_Tag_Model_Tag::STATUS_APPROVED => __('Approved'),
            ),
            'scope_label' => ' ' . __('[GLOBAL]'),
        ));

        $fieldset->addField('base_popularity', 'text', array(
            'name' => 'base_popularity',
            'label' => __('Base Popularity'),
            'title' => __('Base Popularity'),
            'scope_label' => ' ' . __('[STORE VIEW]'),
        ));

        if (!$model->getId() && !Mage::getSingleton('Mage_Adminhtml_Model_Session')->getTagData() ) {
            $model->setStatus(Mage_Tag_Model_Tag::STATUS_APPROVED);
        }

        if ( Mage::getSingleton('Mage_Adminhtml_Model_Session')->getTagData() ) {
            $form->addValues(Mage::getSingleton('Mage_Adminhtml_Model_Session')->getTagData());
            Mage::getSingleton('Mage_Adminhtml_Model_Session')->setTagData(null);
        } else {
            $form->addValues($model->getData());
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
