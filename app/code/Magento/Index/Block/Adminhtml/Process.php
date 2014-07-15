<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Index\Block\Adminhtml;

class Process extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_Index';
        $this->_controller = 'adminhtml_process';
        $this->_headerText = __('Index Management');
        parent::_construct();
        $this->buttonList->remove('add');
    }
}
