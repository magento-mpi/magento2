<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Widget Instance grid container
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Widget\Block\Adminhtml\Widget;

class Instance extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Block constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_Widget';
        $this->_controller = 'adminhtml_widget_instance';
        $this->_headerText = __('Manage Widget Instances');
        parent::_construct();
        $this->_updateButton('add', 'label', __('Add New Widget Instance'));
    }
}
