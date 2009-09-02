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
 * @category   Enterprise
 * @package    Enterprise_Cms
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Cms Widget Instance Settings tab block
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Cms_Block_Adminhtml_Cms_Widget_Instance_Edit_Tab_Settings
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
        return Mage::helper('enterprise_cms')->__('Widget Settings');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('enterprise_cms')->__('Widget Settings');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return !(bool)$this->getWidgetInstance()->isCompleteToCreate();
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

    public function getWidgetInstance()
    {
        return Mage::registry('widget_instance');
    }

    public function setWidgetInstance($widgetInstance)
    {
        return $this;
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
            array('legend'=>Mage::helper('enterprise_cms')->__('Widget Settings'))
        );

        $this->_addElementTypes($fieldset);

        $fieldset->addField('type', 'select', array(
            'name'  => 'type',
            'label' => Mage::helper('enterprise_cms')->__('Type'),
            'title' => Mage::helper('enterprise_cms')->__('Type'),
            'class' => '',
            'values' => $this->getTypesOptionsArray()
        ));

        $fieldset->addField('package_theme', 'select', array(
            'name'  => 'package_theme',
            'label' => Mage::helper('enterprise_cms')->__('Design Package/Theme'),
            'title' => Mage::helper('enterprise_cms')->__('Design Package/Theme'),
            'required' => false,
            'values'   => $this->getPackegeThemeOptionsArray()
        ));
        $continueButton = $this->getLayout()
            ->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'     => Mage::helper('enterprise_cms')->__('Continue'),
                'onclick'   => "setSettings('".$this->getContinueUrl()."', 'type', 'package_theme')",
                'class'     => 'save'
            ));
        $fieldset->addField('continue_button', 'note', array(
            'text' => $continueButton->toHtml(),
        ));

        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getContinueUrl()
    {
        return $this->getUrl('*/*/*', array(
            '_current' => true,
            'type' => '{{type}}',
            'package_theme' => '{{package_theme}}'
        ));
    }

    /**
     * Retrieve array (widget_type => widget_name) of available widgets
     *
     * @return array
     */
    public function getTypesOptionsArray()
    {
        $widgets = array();
        $widgetsXml = Mage::getModel('cms/widget')->getXmlConfig();
        foreach ($widgetsXml->getNode('widgets')->children() as $item) {
            if ($type = $item->getAttribute('type')) {
                $widgets[] = array(
                    'value' => $type,
                    'label' => (string)Mage::helper('enterprise_cms')->__('%s', $item->name)
                );
            }
        }
        return $widgets;
    }

    public function getPackegeThemeOptionsArray()
    {
        return Mage::getModel('core/design_source_design')->getAllOptions(true, true);
    }
}