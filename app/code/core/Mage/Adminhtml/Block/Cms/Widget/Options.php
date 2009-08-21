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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_GoogleBase
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * WYSIWYG widget options form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Cms_Widget_Options extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Default type for widget option field
     */
    const DEFAULT_ELEMENT_TYPE = 'text';

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('options_fieldset', array(
            'legend'    => $this->helper('cms')->__('Widget Options')
        ));


        $form->setUseContainer(false);
        $this->setForm($form);

        $widget = Mage::getSingleton('cms/widget')
            ->getXmlElementByType($this->_getRequestOptionValues('widget_type'));

        if ( !($widget instanceof Varien_Simplexml_Element)) {
            return;
        }

        // Define helper for translations
        if ($widget->getAttribute('module')) {
            $widgetHelper = Mage::helper($widget->getAttribute('module'));
        } else {
            $widgetHelper = Mage::helper('cms');
        }

        if (!$widget->parameters) {
            return;
        }

        // Add Widget Options
        foreach ($widget->parameters->children() as $option) {
            $this->_addField($option, $widgetHelper);
        }
    }

    /**
     * Add field to Options form based on option configuration
     *
     * @param Varien_Simplexml_Element $option
     * @param Mage_Core_Helper_Abstract $helper
     * @return Varien_Data_Form_Element_Abstract
     */
    protected function _addField($option, $helper)
    {
        $form = $this->getForm();
        $fieldset = $form->getElement('options_fieldset');
        $values = $this->_getRequestOptionValues('values');

        // renderer, filter and type for option
        $_renderer = false;
        $_helper = false;
        $_type = false;

        $data = array(
            'name'      => $form->addSuffixToName($option->getName(), 'parameters'),
            'label'     => $helper->__((string)$option->label),
            'required'  => (bool)$option->required,
            'class'     => 'widget-option',
            'note'      => $helper->__((string)$option->note),
        );

        $defaultValue = (string)$option->value;

        // Save Default option value in field note
        if ($defaultValue) {
            $data['required'] = false;
            $data['note'] .= $this->helper('cms')->__("Leave blank for default value '%s'", $defaultValue);
        }

        if (isset($values[$option->getName()]) && $values[$option->getName()] != $defaultValue) {
            $data['value'] = $values[$option->getName()];
        }

        if ($option->values) {
            $data['values'] = $this->_prepareOptionValues($option->values, $helper);
        }

        $type = $option->type;
        if ($type->hasChildren()) {
            $_type = $type->element_type ? (string)$type->element_type : self::DEFAULT_ELEMENT_TYPE;
            if ($type->element_helper) {
                $_helper = $this->getLayout()->getBlockSingleton( (string)$type->element_helper );
            }
        } elseif (strstr($type, '/')) {
            $_type = self::DEFAULT_ELEMENT_TYPE;
            $_renderer = $this->getLayout()->createBlock( (string)$type );
        } elseif (!$option->visible) {
            $_type = 'hidden';
        } else {
            $_type = (string)$type;
        }

        $field = $fieldset->addField('option_' . $option->getName(), $_type, $data);

        // Render field or add extra html
        if ($_renderer) {
            $field->setRenderer($_renderer);
        } elseif ($_helper) {
            $_helper->prepareElementHtml($field);
        }

        return $field;
    }

    /**
     * Prepare values array for HTML (multi)select form element
     *
     * @param Mage_Core_Model_Config_Element
     * @param Mage_Core_Helper_Abstract $helper
     * @return array
     */
    protected function _prepareOptionValues($values, $helper)
    {
        $result = array();
        if ($values->hasChildren()) {
            foreach ($values->children() as $child) {
                $result[] = array(
                    'value' => $child->getName(),
                    'label' => $helper->__((string)$child)
                );
            }
        } else {
            $source = Mage::getModel( (string)$values );
            if (is_object($source)) {
                $result = $source->toOptionArray();
            }
        }
        return $result;
    }

    /**
     * Retrieve values for Options from Request (widget callback)
     *
     * @param string $key Key name to get specified Request data
     * @return string|array
     */
    protected function _getRequestOptionValues($key = null)
    {
        if (!$this->hasData('request_values')) {
            $values = $this->getRequest()->getParam('widget');
            $values = Mage::helper('core')->jsonDecode($values);
            $this->setData('request_values', $values);
        }
        $values = $this->getData('request_values');
        if ($key !== null) {
            return isset($values[$key]) ? $values[$key] : null;
        }
        return $values;
    }
}
