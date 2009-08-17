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
 * CMS WYSIWYG widget plugin form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Cms_Page_Edit_Wysiwyg_Widget_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Form with widget to select
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'    => $this->helper('cms')->__('Widget')
        ));

        $select = $fieldset->addField('select_widget_code', 'select', array(
            'label'                 => $this->helper('cms')->__('Widget Type'),
            'title'                 => $this->helper('cms')->__('Widget Type'),
            'name'                  => 'widget_code',
            'required'              => true,
            'options'               => $this->_getWidgetSelectOptions(),
            'note'                  => $this->helper('cms')->__('No options available'),
            'after_element_html'    => $this->_getWidgetSelectAfterHtml(),
        ));

        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setMethod('post');
        $form->setAction($this->getUrl('*/*/buildWidget'));
        $this->setForm($form);
    }

    /**
     * Prepare options for widgets HTML select
     *
     * @return array
     */
    protected function _getWidgetSelectOptions()
    {
        $options = array('' => $this->helper('cms')->__('Select widget to load its options'));
        foreach ($this->_getAvailableWidgets() as $code => $data) {
            $options[$code] = $data['name'];
        }
        return $options;
    }

    /**
     * Prepare widgets select after element HTML
     *
     * @return string
     */
    protected function _getWidgetSelectAfterHtml()
    {
        $html =  '';
        foreach ($this->_getAvailableWidgets() as $code => $data) {
            $html .= sprintf('<div id="%s-description" class="no-display">%s</div>',
                $code,
                $data['description']
            );
        }
        return $html;
    }

    /**
     * Return array of available widgets based on configuration
     *
     * @return array
     */
    protected function _getAvailableWidgets()
    {
        if (!$this->getData('available_widgets')) {
            $config = Mage::getSingleton('cms/page_wysiwyg_widget')->getXmlConfig();
            $widgets = $config->getNode('widgets');
            $result = array();
            foreach ($widgets->children() as $widget) {
                if ($widget->getAttribute('module')) {
                    $helper = Mage::helper($widget->getAttribute('module'));
                } else {
                    $helper = Mage::helper('cms');
                }
                $result[$widget->getName()] = array(
                    'name'          => $helper->__((string)$widget->name),
                    'description'   => $helper->__((string)$widget->description),
                    'type'          => (string)$widget->type,
                );
            }
            $this->setData('available_widgets', $result);
        }
        return $this->getData('available_widgets');
    }
}
