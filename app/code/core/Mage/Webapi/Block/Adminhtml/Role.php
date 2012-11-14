<?php
/**
 * Web API Adminhtml role block
 *
 * @copyright {}
 */
class Mage_Webapi_Block_Adminhtml_Role extends Mage_Backend_Block_Widget_Grid_Container
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Mage_Webapi';
        $this->_controller = 'adminhtml_role';
        $this->_headerText = $this->__('API Roles');
        $this->_addButtonLabel = $this->__('Add New API Role');
        parent::_construct();
    }

    /**
     * Get create URL
     *
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/*/edit');
    }
}
