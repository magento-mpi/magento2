<?php
/**
 * Adminhtml AdminNotification inbox grid
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_AdminNotification
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_AdminNotification_Block_Inbox extends Mage_Backend_Block_Widget_Grid_Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml';
        $this->_blockGroup = 'Mage_AdminNotification';
        $this->_headerText = __('Messages Inbox');
        parent::_construct();
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->_removeButton('add');
        return $this;
    }
}
