<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * WYSIWYG widget plugin form
 *
 * @category   Mage
 * @package    Mage_Widget
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Widget_Block_Adminhtml_Widget_Form extends Magento_Adminhtml_Block_Widget_Form
{
    /**
     * Form with widget to select
     */
    protected function _prepareForm()
    {
        $form = new Magento_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'    => $this->helper('Mage_Widget_Helper_Data')->__('Widget')
        ));

        $select = $fieldset->addField('select_widget_type', 'select', array(
            'label'                 => $this->helper('Mage_Widget_Helper_Data')->__('Widget Type'),
            'title'                 => $this->helper('Mage_Widget_Helper_Data')->__('Widget Type'),
            'name'                  => 'widget_type',
            'required'              => true,
            'options'               => $this->_getWidgetSelectOptions(),
            'after_element_html'    => $this->_getWidgetSelectAfterHtml(),
        ));

        $form->setUseContainer(true);
        $form->setId('widget_options_form');
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
        foreach ($this->_getAvailableWidgets(true) as $data) {
            $options[$data['type']] = $data['name'];
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
        $html = '<p class="nm"><small></small></p>';
        $i = 0;
        foreach ($this->_getAvailableWidgets(true) as $data) {
            $html .= sprintf('<div id="widget-description-%s" class="no-display">%s</div>', $i, $data['description']);
            $i++;
        }
        return $html;
    }

    /**
     * Return array of available widgets based on configuration
     *
     * @return array
     */
    protected function _getAvailableWidgets($withEmptyElement = false)
    {
        if (!$this->hasData('available_widgets')) {
            $result = array();
            $allWidgets = Mage::getModel('Mage_Widget_Model_Widget')->getWidgetsArray();
            $skipped = $this->_getSkippedWidgets();
            foreach ($allWidgets as $widget) {
                if (is_array($skipped) && in_array($widget['type'], $skipped)) {
                    continue;
                }
                $result[] = $widget;
            }
            if ($withEmptyElement) {
                array_unshift($result, array(
                    'type'        => '',
                    'name'        => $this->helper('Magento_Adminhtml_Helper_Data')->__('-- Please Select --'),
                    'description' => '',
                ));
            }
            $this->setData('available_widgets', $result);
        }

        return $this->_getData('available_widgets');
    }

    /**
     * Return array of widgets disabled for selection
     *
     * @return array
     */
    protected function _getSkippedWidgets()
    {
        return Mage::registry('skip_widgets');
    }
}
