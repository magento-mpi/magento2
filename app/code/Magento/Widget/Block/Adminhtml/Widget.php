<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * WYSIWYG widget plugin main block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Widget\Block\Adminhtml;

class Widget extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_blockGroup = 'Magento_Widget';
        $this->_controller = 'adminhtml';
        $this->_mode = 'widget';
        $this->_headerText = __('Widget Insertion');

        $this->removeButton('reset');
        $this->removeButton('back');
        $this->buttonList->update('save', 'label', __('Insert Widget'));
        $this->buttonList->update('save', 'class', 'add-widget');
        $this->buttonList->update('save', 'id', 'insert_button');
        $this->buttonList->update('save', 'onclick', 'wWidget.insertWidget()');
        $this->buttonList->update('save', 'region', 'footer');

        $this->_formScripts[] = 'wWidget = new WysiwygWidget.Widget(' .
            '"widget_options_form", "select_widget_type", "widget_options", "' .
            $this->getUrl(
                'adminhtml/*/loadOptions'
            ) . '", "' . $this->getRequest()->getParam(
                'widget_target_id'
            ) . '");';
    }
}
