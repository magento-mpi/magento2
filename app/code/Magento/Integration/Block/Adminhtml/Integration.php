<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Block\Adminhtml;

/**
 * Integration block.
 */
class Integration extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Define actions available on the integrations grid page.
     */
    protected function _construct()
    {
        /*
        $this->addData(array(
            \Magento\Backend\Block\Widget\Container::PARAM_CONTROLLER => 'adminhtml_integration',
            \Magento\Backend\Block\Widget\Grid\Container::PARAM_BLOCK_GROUP => 'Magento_Integration',
            \Magento\Backend\Block\Widget\Grid\Container::PARAM_BUTTON_NEW => __('Add New Integration'),
            \Magento\Backend\Block\Widget\Container::PARAM_HEADER_TEXT => __('Integrations'),
        ));
        parent::_construct();
        $this->_addNewButton();
        */
        $this->_controller = 'adminhtml_integration';
        $this->_blockGroup = 'Magento_Integration';
        $this->_headerText = __('Integrations');
        $this->_addButtonLabel = __('Add New Integration');
        parent::_construct();
    }
}
