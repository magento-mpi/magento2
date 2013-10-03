<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Index
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Index\Block\Adminhtml;

class Process extends \Magento\Adminhtml\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_Index';
        $this->_controller = 'adminhtml_process';
        $this->_headerText = __('Index Management');
        parent::_construct();
        $this->_removeButton('add');
    }
}
