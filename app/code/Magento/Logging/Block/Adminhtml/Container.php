<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Logging\Block\Adminhtml;

/**
 * General Logging container
 */
class Container extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Remove add button
     * Set block group and controller
     *
     * @return void
     */
    protected function _construct()
    {
        $action = $this->_request->getActionName();
        $this->_blockGroup = 'Magento_Logging';
        $this->_controller = 'adminhtml_' . $action;

        parent::_construct();
        $this->_removeButton('add');
    }

    /**
     * Header text getter
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __($this->getData('header_text'));
    }
}
