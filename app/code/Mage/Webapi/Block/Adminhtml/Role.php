<?php
/**
 * Web API Adminhtml role block.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Block_Adminhtml_Role extends Magento_Backend_Block_Widget_Grid_Container
{
    /**
     * @var string
     */
    protected $_blockGroup = 'Mage_Webapi';

    /**
     * @var string
     */
    protected $_controller = 'adminhtml_role';

    /**
     * Internal constructor.
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_headerText = $this->__('API Roles');
        $this->_updateButton('add', 'label', $this->__('Add New API Role'));
    }

    /**
     * Get create URL.
     *
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/*/edit');
    }
}
