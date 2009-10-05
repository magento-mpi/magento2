<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Cms Widget Instance Main tab block
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Cms_Block_Adminhtml_Cms_Widget_Instance_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _construct()
    {
        parent::_construct();
        $this->setActive(true);
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('enterprise_cms')->__('Frontend Properties');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('enterprise_cms')->__('Frontend Properties');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return $this->getWidgetInstance()->isCompleteToCreate();
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Getter
     *
     * @return Enterprise_Cms_Model_Widget_Instance
     */
    public function getWidgetInstance()
    {
        return Mage::registry('current_widget_instance');
    }

    protected function _prepareForm()
    {
        $widgetInstance = $this->getWidgetInstance();
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post'
        ));

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend' => Mage::helper('enterprise_cms')->__('Frontend Properties'))
        );

        if ($widgetInstance->getId()) {
            $fieldset->addField('instance_id', 'hidden', array(
                'name' => 'isntance_id',
            ));
        }

        $this->_addElementTypes($fieldset);

        $fieldset->addField('type', 'select', array(
            'name'  => 'type',
            'label' => Mage::helper('enterprise_cms')->__('Type'),
            'title' => Mage::helper('enterprise_cms')->__('Type'),
            'class' => '',
            'values' => $this->getTypesOptionsArray(),
            'disabled' => true
        ));

        $fieldset->addField('package_theme', 'select', array(
            'name'  => 'package_theme',
            'label' => Mage::helper('enterprise_cms')->__('Design Package/Theme'),
            'title' => Mage::helper('enterprise_cms')->__('Design Package/Theme'),
            'required' => false,
            'values'   => $this->getPackegeThemeOptionsArray(),
            'disabled' => true
        ));

        $fieldset->addField('title', 'text', array(
            'name'  => 'title',
            'label' => Mage::helper('enterprise_cms')->__('Widget Instance Title'),
            'title' => Mage::helper('enterprise_cms')->__('Widget Instance Title'),
            'class' => '',
            'required' => true,
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_ids', 'multiselect', array(
                'name'      => 'store_ids[]',
                'label'     => Mage::helper('enterprise_cms')->__('Assign to Store Views'),
                'title'     => Mage::helper('enterprise_cms')->__('Assign to Store Views'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
        }

        $fieldset->addField('sort_order', 'text', array(
            'name'  => 'sort_order',
            'label' => Mage::helper('enterprise_cms')->__('Sort Order'),
            'title' => Mage::helper('enterprise_cms')->__('Sort Order'),
            'class' => '',
            'required' => false,
            'note' => Mage::helper('enterprise_cms')->__('Sort Order of widget instances in the same block reference')
        ));

        /* @var $layoutBlock Enterprise_Cms_Block_Adminhtml_Cms_Widget_Instance_Edit_Tab_Main_Layout */
        $layoutBlock = $this->getLayout()
            ->createBlock('enterprise_cms/adminhtml_cms_widget_instance_edit_tab_main_layout')
            ->setWidgetInstance($widgetInstance);
        $fieldset = $form->addFieldset('layout_updates_fieldset',
            array('legend' => Mage::helper('enterprise_cms')->__('Layout Updates'))
        );
        $fieldset->addField('layout_updates', 'note', array(
        ));
        $form->getElement('layout_updates_fieldset')->setRenderer($layoutBlock);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Retrieve array (widget_type => widget_name) of available widgets
     *
     * @return array
     */
    public function getTypesOptionsArray()
    {
        return $this->getWidgetInstance()->getWidgetsOptionArray();
    }

    public function getPackegeThemeOptionsArray()
    {
        return Mage::getModel('core/design_source_design')
            ->setIsFullLabel(true)->getAllOptions(true);
    }

    protected function _initFormValues()
    {
        $this->getForm()->addValues($this->getWidgetInstance()->getData());
        return parent::_initFormValues();
    }
}
