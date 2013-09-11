<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * General Logging container
 */
namespace Magento\Logging\Block\Adminhtml;

class Container extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Remove add button
     * Set block group and controller
     *
     */
    protected function _construct()
    {
        $action = \Mage::app()->getRequest()->getActionName();
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
