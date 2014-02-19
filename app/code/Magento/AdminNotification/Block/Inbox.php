<?php
/**
 * Adminhtml AdminNotification inbox grid
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdminNotification
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdminNotification\Block;

class Inbox extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml';
        $this->_blockGroup = 'Magento_AdminNotification';
        $this->_headerText = __('Messages Inbox');
        parent::_construct();
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->_removeButton('add');
        return $this;
    }
}
