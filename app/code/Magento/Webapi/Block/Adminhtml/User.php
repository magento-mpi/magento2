<?php
/**
 * Web API adminhtml user block.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Block_Adminhtml_User extends Magento_Backend_Block_Widget_Grid_Container
{
    /**
     * @var string
     */
    protected $_blockGroup = 'Magento_Webapi';

    /**
     * @var string
     */
    protected $_controller = 'adminhtml_user';

    /**
     * Internal constructor.
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_headerText = __('API Users');
        $this->_updateButton('add', 'label', __('Add New API User'));
    }
}
