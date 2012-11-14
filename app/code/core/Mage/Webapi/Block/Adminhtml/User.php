<?php
/**
 * Web API permissions user block
 *
 * @copyright {}
 */
class Mage_Webapi_Block_Adminhtml_User extends Mage_Backend_Block_Widget_Grid_Container
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Mage_Webapi';
        $this->_controller = 'adminhtml_user';
        $this->_headerText = $this->__('API Users');
        $this->_addButtonLabel = $this->__('Add New API User');
        parent::_construct();
    }

    /**
     * Prepare output HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->_eventManager->dispatch('webapi_user_html_before', array('block' => $this));
        return parent::_toHtml();
    }
}
